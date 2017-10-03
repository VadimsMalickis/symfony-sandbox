<?php


namespace TreeBundle\Command\Exception;


class InvalidArgumentException extends \Exception
{

    /**
     * InvalidArgumentException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}