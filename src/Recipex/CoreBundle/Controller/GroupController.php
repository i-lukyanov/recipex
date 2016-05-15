<?php

namespace Recipex\CoreBundle\Controller;

use Recipex\CoreBundle\Entity\Group;
use Recipex\CoreBundle\Exceptions\ApiProblemException;
use Recipex\CoreBundle\Form\GroupType;
use Recipex\CoreBundle\Traits\FormErrorsTrait;
use Recipex\CoreBundle\Utils\ApiProblem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Extra\Route("/groups")
 */
class GroupController extends ApiController
{
    use FormErrorsTrait;

    /**
     * Создание группы
     *
     * @param Request $request
     *
     * @Extra\Route("", name="api_group_create")
     * @Extra\Method("POST")
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws ApiProblemException
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = $request->request->all();
        $data['name'] = str_replace(' ', '_', strtolower(\transliterate_ru($data['displayName'])));
        /** @var UploadedFile $image */
        $image = $request->files->get('image');
        if (empty($image)) {
            $apiProblem = new ApiProblem(Response::HTTP_BAD_REQUEST, ApiProblem::TYPE_VALIDATION_ERROR);
            $apiProblem->set('errors', ['image' => [$this->get('translator')->trans('file_missing', [], 'RecipexCoreBundle')]]);

            throw new ApiProblemException($apiProblem);
        }

        $data['image']['name'] = $data['name'] . '_logo.' . $image->getClientOriginalExtension();
        $data['image']['path'] = realpath($this->getParameter('web_images_path')) . DIRECTORY_SEPARATOR . $data['image']['name'];
        $data['image']['extension'] = $image->getClientOriginalExtension();
        $data['image']['size'] = $image->getClientSize();

        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->submit($data);
        if (!$form->isValid()) {
            $apiProblem = new ApiProblem(Response::HTTP_BAD_REQUEST, ApiProblem::TYPE_VALIDATION_ERROR);
            $apiProblem->set('errors', $this->getErrorsFromForm($form));

            throw new ApiProblemException($apiProblem);
        }

        $em->persist($group);
        $em->flush();

        $image->move(realpath($this->getParameter('web_images_path')), $data['image']['name']);

        $group_array = $this->container->get('serializer')->normalize($group, 'json', ['groups' => ['get']]);

        return $this->handleApiResponse($group_array, Response::HTTP_CREATED);
    }

    /**
     * Просмотр списка групп
     *
     * @Extra\Route("", name="api_group_list")
     * @Extra\Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws ApiProblemException
     */
    public function listAction()
    {
        $groups = $this->getDoctrine()->getRepository('RecipexCoreBundle:Group')->findAll();
        $group_array = $this->container->get('serializer')->normalize($groups, 'json', ['groups' => ['list']]);

        return $this->handleApiResponse($group_array, Response::HTTP_OK);
    }
}
