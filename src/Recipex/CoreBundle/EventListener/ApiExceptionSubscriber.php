<?php
/**
 * Author: Ivan Lukyanov
 * Date: 27.03.2016
 */

namespace Recipex\CoreBundle\EventListener;

use Recipex\CoreBundle\Exceptions\ApiProblemException;
use Recipex\CoreBundle\Utils\ApiProblem;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    private $debug;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();
        $statusCode = ($e instanceof HttpExceptionInterface) ? $e->getStatusCode() : 500;

        if ($statusCode == 500 && $this->debug) {
            return;
        }

        if ($e instanceof ApiProblemException) {
            $apiProblem = $e->getApiProblem();
        } else {
            $apiProblem = new ApiProblem($statusCode);

            if ($e instanceof HttpExceptionInterface || $e instanceof FlattenException) {
                $apiProblem->set('detail', $e->getMessage());
            }
        }

        $response = new JsonResponse(
            $apiProblem->toArray(),
            $apiProblem->getStatusCode(),
            ['Content-Type' => 'application/problem+json']
        );

        $event->setResponse($response);
    }
    
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }
}