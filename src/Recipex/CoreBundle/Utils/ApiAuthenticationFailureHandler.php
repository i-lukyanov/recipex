<?php
/**
 * Author: lukianov
 * Date: 6/6/16
 */

namespace Recipex\CoreBundle\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class ApiAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    const RESPONSE_CODE    = 401;
    const RESPONSE_MESSAGE = 'Bad credentials';

    /**
     * @var ApiResponseFactory
     */
    private $responseFactory;


    /**
     * @param ApiResponseFactory $responseFactory
     */
    public function __construct(ApiResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $apiProblem = new ApiProblem(self::RESPONSE_CODE);
        $apiProblem->set('detail', self::RESPONSE_MESSAGE);

        return $this->responseFactory->createResponse($apiProblem);
    }
}