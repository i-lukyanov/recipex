<?php

namespace Recipex\CoreBundle\Controller;

use Recipex\CoreBundle\Exceptions\ApiProblemException;
use Recipex\CoreBundle\Traits\FormErrorsTrait;
use Recipex\CoreBundle\Utils\ApiProblem;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;

/**
 * @Extra\Route("/auth")
 */
class RegistrationController extends ApiController
{
    use FormErrorsTrait;

    /**
     * Регистрация пользователя системы
     *
     * @param Request $request
     * @return Response
     *
     * @Extra\Route("/register", name="api_register")
     * @Extra\Method({"POST"})
     */
    public function registerAction(Request $request)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
        $translator = $this->container->get('translator');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $content = $request->getContent();
        $jsonData = json_decode($content, true);

        if ($jsonData === null) {
            $apiProblem = new ApiProblem(Response::HTTP_BAD_REQUEST, ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT);
            $apiProblem->set('errors', ['body' => $translator->trans('invalid_body', [], 'RecipexCoreBundle')]);

            throw new ApiProblemException($apiProblem);
        }

        $form->submit($jsonData);

        if (!$form->isValid()) {
            $apiProblem = new ApiProblem(Response::HTTP_BAD_REQUEST, ApiProblem::TYPE_VALIDATION_ERROR);
            $apiProblem->set('errors', $this->getErrorsFromForm($form));

            throw new ApiProblemException($apiProblem);
        }

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

        $userManager->updateUser($user);

        $user_array = $this->container->get('serializer')->normalize($user, 'json', ['groups' => ['list']]);

        return $this->handleApiResponse($user_array, Response::HTTP_CREATED);
    }
}
