<?php
/**
 * Author: Ivan Lukyanov
 * Date: 27.03.2016
 */

namespace Recipex\CoreBundle\Controller;

use Recipex\CoreBundle\Utils\ApiProblem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    /**
     * Возврат успешного ответа клиенту API
     *
     * @param array $content
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function handleApiResponse(array $content, $statusCode)
    {
        $response = new JsonResponse(
            $content,
            $statusCode,
            ['Content-Type' => 'application/json']
        );

        return $response;
    }

    /**
     * Возврат ошибок клиенту API
     *
     * @param ApiProblem $apiProblem
     * @return JsonResponse
     */
    protected function handleApiProblemResponse(ApiProblem $apiProblem)
    {
        $response = new JsonResponse(
            $apiProblem->toArray(),
            $apiProblem->getStatusCode(),
            ['Content-Type' => 'application/problem+json']
        );

        return $response;
    }
}