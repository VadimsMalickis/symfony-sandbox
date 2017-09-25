<?php


namespace AppBundle\Tree\Exception;


class LeafParseValidationExceptionBuilder
{

    const ILLEGAL_CHAR_AT_POSITION              = 'Illegal character "%s" at position %u';
    const INVALID_INPUT_FORMAT                  = 'Invalid input format';

    /** @var string */
    private $messageTpl;

    /** @var string */
    private $char;

    /** @var int */
    private $position;

    /**
     * @return $this
     */
    public function setIllegalCharAtPosition()
    {
        $this->messageTpl = self::ILLEGAL_CHAR_AT_POSITION;
        return $this;
    }

    /**
     * @return $this
     */
    public function setInvalidInputFormat()
    {
        $this->messageTpl = self::INVALID_INPUT_FORMAT;
        return $this;
    }

    /**
     * @param string $char
     * @return LeafParseValidationExceptionBuilder
     */
    public function setChar(string $char): LeafParseValidationExceptionBuilder
    {
        $this->char = $char;
        return $this;
    }

    /**
     * @param int $position
     * @return LeafParseValidationExceptionBuilder
     */
    public function setPosition(int $position): LeafParseValidationExceptionBuilder
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return LeafParseValidationException
     */
    public function build()
    {
        $message = sprintf($this->messageTpl, $this->char, $this->position);
        return new LeafParseValidationException($message);
    }
}