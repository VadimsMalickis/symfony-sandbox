<?php


namespace AppBundle\Tree;


class Leaf {

    /** @var  int */
    private $value;

    /** @var Leaf[] */
    private $childs = [];

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return Leaf
     */
    public function setValue(int $value): Leaf
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return Leaf[]
     */
    public function getChilds(): array
    {
        return $this->childs;
    }

    /**
     * @param Leaf $child
     * @return Leaf
     */
    public function addChild($child): Leaf
    {
        $this->childs[] = $child;
        return $this;
    }

    public function findMax()
    {
        $max = null;
        foreach ($this->childs as $child) {
            $current = $child->findMax();
            if ($max === null || $current > $max) {
                $max = $current;
            }
        }
        if ($max === null || $max < $this->value) {
            $max = $this->value;
        }
        return $max;
    }

    public function findMaxSum()
    {
        $maxSum = null;
        foreach ($this->childs as $child) {
            $branchSum = $child->findMaxSum();
            if ($maxSum === null || $branchSum > $maxSum) {
                $maxSum = $branchSum;
            }
        }
        return $this->value + ($maxSum === null ? 0 : $maxSum);
    }

    public function toJSON($level = 0)
    {
        $json = '{"value": ' . $this->value . ', "childs": [';
        $isFirst = true;
        foreach ($this->childs as $child) {
            $json .= (!$isFirst ? ',' : '') . "\n" . str_repeat("    ", $level + 1)
                . $child->toJSON($level + 1);
            $isFirst = false;
        }
        $json .= (!$isFirst ? "\n" . str_repeat("    ", $level) : "") . "]}";
        return $json;
    }

//    public function sum()
//    {
//        return $this->value
//            + ($this->left !== null ? $this->left->sum() : 0)
//            + ($this->right !== null ? $this->right->sum() : 0)
//            + ($this->middle !== null ? $this->middle->sum(): 0);
//    }
//
//    public function buildLeafTree()
//    {
//
//    }
//
//    public function sumChilds() {
//        $sum = $this->value;
//        foreach ($this->childs as $child) {
//            $sum += $child->sumChilds();
//        }
//        return $sum;
//    }
//
//    public function arraySum( array $arr) {
//        $sum = 0;
//        foreach ($arr as $value) {
//            $sum += $value;
//        }
//        return $sum;
//    }
}
