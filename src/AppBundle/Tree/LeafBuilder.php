<?php


namespace AppBundle\Tree;


use AppBundle\Tree\Exception\LeafParseValidationException;
use AppBundle\Tree\Exception\LeafParseValidationExceptionBuilder;

class LeafBuilder
{
    const SINGLE_CHILD_DELIM = ',';
    const CHILD_SET_DELIM = ';';
    const ALLOWED_CHARACTERS = [' ', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', self::SINGLE_CHILD_DELIM, self::CHILD_SET_DELIM, '-'];

    /**
     * Input is '1;2,3;;4,5'
     * @param string $input
     * @return Leaf
     * @throws LeafParseValidationException
     */
    public function buildTree(string $input): Leaf
    {
        /** @var Leaf[] $unprocessed */
        $unprocessed = [];
        /** @var Leaf $tree */
        $tree = null;

        if (empty($input)) {
            throw (new LeafParseValidationExceptionBuilder())
                ->setInvalidInputFormat()
                ->build();
        }

        // store your state
        $state = new CurrentValueState();

        $inputLength = strlen($input);
        for ($i = 0; $i < $inputLength; $i++) {
            $char = $input[$i];

            $state->process($char, $i);

            if ($state->isValueRead() || $i === $inputLength - 1) {

                if ($tree !== null && empty($unprocessed)) {
                    throw (new LeafParseValidationExceptionBuilder())
                        ->setInvalidInputFormat()
                        ->build();
                }

                if ($state->isEmptyChildSet()) {
                    array_shift($unprocessed);
                    $state->reset();
                    continue;
                }

                $leaf = new Leaf($state->buildInteger());
                $unprocessed[] = $leaf;

                if ($tree === null) {
                    $tree = $leaf;
                    $state->setRootNodeExists(true);

                } else {
                    $unprocessed[0]->addChild($leaf);

                    if ($state->isChildSetRead()) {
                        array_shift($unprocessed);
                    }
                }

                $state->reset();
            }

        }
        return $tree;
    }

}

class CurrentValueState {

    /** @var string */
    private $value;
    /** @var bool */
    private $hasMinus;
    /** @var bool */
    private $singleChildRead;
    /** @var bool */
    private $childSetRead;
    /** @var bool */
    private $rootNodeExists;

    /**
     * CurrentValueState constructor.
     */
    public function __construct()
    {
        $this->rootNodeExists = false;
        $this->reset();
    }

    /**
     * Resets the state
     */
    public function reset()
    {
        $this->value = '';
        $this->hasMinus = false;
        $this->singleChildRead = false;
        $this->childSetRead = false;
    }

    /**
     * @param string $char
     * @param int $position
     * @throws LeafParseValidationException
     */
    public function process($char, $position)
    {
        // allowing only whitelisted chars
        if (!in_array($char, LeafBuilder::ALLOWED_CHARACTERS)) {
            throw (new LeafParseValidationExceptionBuilder())
                ->setIllegalCharAtPosition()
                ->setChar($char)
                ->setPosition($position)
                ->build();
        }

        // ignoring whitespace
        if ($char === ' ') {
            return;
        }

        // empty values are not allowed
        if ($char === LeafBuilder::SINGLE_CHILD_DELIM && empty($this->value)) {
            throw (new LeafParseValidationExceptionBuilder())
                ->setIllegalCharAtPosition()
                ->setChar($char)
                ->setPosition($position)
                ->build();
        }

        if (is_numeric($char)) {
            $this->value .= $char;

        } else if ($char === '-') {
            if ($this->hasMinus) {
                throw (new LeafParseValidationExceptionBuilder())
                    ->setIllegalCharAtPosition()
                    ->setChar($char)
                    ->setPosition($position)
                    ->build();
            }
            if (!empty($this->value)) {
                throw (new LeafParseValidationExceptionBuilder())
                    ->setIllegalCharAtPosition()
                    ->setChar($char)
                    ->setPosition($position)
                    ->build();
            }
            $this->value = '-';
            $this->hasMinus = true;

        } else if ($char === LeafBuilder::SINGLE_CHILD_DELIM) {

            if (!$this->rootNodeExists) {
                throw (new LeafParseValidationExceptionBuilder())
                    ->setIllegalCharAtPosition()
                    ->setChar($char)
                    ->setPosition($position)
                    ->build();
            }
            $this->singleChildRead = true;

        } else if ($char === LeafBuilder::CHILD_SET_DELIM) {
            $this->childSetRead = true;

        }
    }

    /**
     * @return int
     */
    public function buildInteger()
    {
        return (int) $this->value;
    }

    /**
     * @return bool
     */
    public function isEmptyChildSet()
    {
        return empty($this->value) && $this->childSetRead;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isSingleChildRead(): bool
    {
        return $this->singleChildRead;
    }

    /**
     * @return bool
     */
    public function isChildSetRead(): bool
    {
        return $this->childSetRead;
    }

    /**
     * @return bool
     */
    public function isValueRead(): bool
    {
        return $this->singleChildRead || $this->childSetRead;
    }

    /**
     * @param bool $rootNodeExists
     * @return CurrentValueState
     */
    public function setRootNodeExists(bool $rootNodeExists): CurrentValueState
    {
        $this->rootNodeExists = $rootNodeExists;
        return $this;
    }

}