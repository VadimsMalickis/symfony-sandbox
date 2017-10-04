<?php


namespace TreeBundle\SimpleNumberCache\Exception;

class KeyNotFound extends \Exception
{
    /**
     * KeyNotFound constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}