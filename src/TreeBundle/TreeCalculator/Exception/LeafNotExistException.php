<?php


namespace TreeBundle\TreeCalculator\Exception;


class LeafNotExistException extends \Exception
{

    /**
     * LeafNotExistException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}