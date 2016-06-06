<?php
/**
 * Author: lukianov
 * Date: 6/6/16
 */

namespace Recipex\CoreBundle\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponseFactory
{
    /**
     * @param ApiProblem $apiProblem
     * @return JsonResponse
     */
    public function createResponse(ApiProblem $apiProblem)
    {
        $response = new JsonResponse(
            $apiProblem->toArray(),
            $apiProblem->getStatusCode(),
            ['Content-Type' => 'application/problem+json']
        );

        return $response;
    }
}