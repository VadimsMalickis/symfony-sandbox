<?php


namespace TreeBundle\Command\Exception;


class InvalidCommandException extends \Exception
{

    /**
     * InvalidCommandException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}