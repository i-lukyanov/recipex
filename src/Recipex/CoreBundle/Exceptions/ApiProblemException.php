<?php
/**
 * Author: Ivan Lukyanov
 * Date: 27.03.2016
 */

namespace Recipex\CoreBundle\Exceptions;

use Recipex\CoreBundle\Utils\ApiProblem;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    private $apiProblem;

    public function __construct(ApiProblem $apiProblem, \Exception $previous = null, array $headers = [], $code = 0)
    {
        $this->apiProblem = $apiProblem;

        parent::__construct($apiProblem->getStatusCode(), $apiProblem->getTitle(), $previous, $headers, $code);
    }

    /**
     * @return ApiProblem
     */
    public function getApiProblem()
    {
        return $this->apiProblem;
    }
}