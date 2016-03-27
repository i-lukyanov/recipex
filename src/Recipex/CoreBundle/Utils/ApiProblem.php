<?php
/**
 * Author: Ivan Lukyanov
 * Date: 04.03.2016
 */

namespace Recipex\CoreBundle\Utils;


use Symfony\Component\HttpFoundation\Response;

class ApiProblem
{
    const TYPE_VALIDATION_ERROR = 'validation_error';
    const TYPE_INVALID_REQUEST_BODY_FORMAT = 'invalid_body_format';

    private static $titles = [
        self::TYPE_VALIDATION_ERROR => 'There was a validation error',
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => 'Invalid JSON format sent',
    ];

    /**
     * @var int Код ответа
     */
    private $statusCode;

    /**
     * @var string Тип ответа
     */
    private $type;

    /**
     * @var string Описание типа ответа
     */
    private $title;

    /**
     * @var array Дополнительные поля
     */
    private $extraData = [];

    /**
     * @param $statusCode
     * @param $type
     */
    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;
        if ($type === null) {
            $type = 'about:blank';
            $title = (isset(Response::$statusTexts[$statusCode])) ? Response::$statusTexts[$statusCode] : 'Unknown status code';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException('No title for type ' . $type);
            }
            $title = self::$titles[$type];
        }

        $this->type = $type;
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->extraData,
            [
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' => $this->title,
            ]
        );
    }

    /**
     * Установка дополнительных данных
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
