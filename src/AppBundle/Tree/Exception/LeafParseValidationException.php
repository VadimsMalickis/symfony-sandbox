<?php


namespace AppBundle\Tree\Exception;

class LeafParseValidationException extends \Exception
{
    /**
     * LeafParseValidationException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }

}