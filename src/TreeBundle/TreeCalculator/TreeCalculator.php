<?php


namespace TreeBundle\TreeCalculator;


use AppBundle\Tree\Leaf;
use AppBundle\Tree\LeafBuilder;
use TreeBundle\SimpleNumberCache\SimpleNumberCache;
use TreeBundle\TreeCalculator\Exception\LeafNotExistException;

class TreeCalculator
{
    /** @var string[] */
    private $treeList = [];

    /** @var SimpleNumberCache */
    private $sumCache;

    /**
     * TreeCalculator constructor.
     */
    public function __construct()
    {
        $this->sumCache = new SimpleNumberCache();
    }

    /**
     * @param string $name
     */
    public function createTree($name)
    {
        $this->treeList[$name] = '';
    }

    /**
     * @param string $name
     * @param string $level
     * @throws LeafNotExistException
     */
    public function addLevel($name, $level)
    {
        $this->validateIfTreeRegistered($name);
        $delim = '';
        if ($this->treeList[$name] !== '') {
            $delim = ';';
        }
        $this->treeList[$name] .= $delim . $level;

        $this->sumCache->invalidate($name);
    }

    /**
     * @param string $name
     * @return int
     */
    public function calculateMaxSum($name)
    {
        $this->validateIfTreeRegistered($name);

        if ($this->sumCache->exists($name)) {
            return $this->sumCache->getFromCache($name);

        } else {
            /** @var Leaf $leaf */
            $leaf = (new LeafBuilder())->buildTree($this->treeList[$name]);
            $maxSum = $leaf->findMaxSum();
            $this->sumCache->store($name, $maxSum);

            return $maxSum;
        }
    }

    /**
     * @param string $name
     * @return string
     */
    public function printTree($name)
    {
        $this->validateIfTreeRegistered($name);
        return $this->treeList[$name];
    }

    /**
     * @param $name
     * @throws LeafNotExistException
     */
    private function validateIfTreeRegistered($name)
    {
        if (!array_key_exists($name, $this->treeList)) {
            throw new LeafNotExistException('Leaf not exist, create it first');
        }
    }
}