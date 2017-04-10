<?php

namespace umulmrum\PhpReferenceChecker\DataModel;

/**
 * MethodRepository represents a collection of method names with reference
 * returns and non reference returns and their counts.
 */
class MethodRepository
{
    /**
     * @var array
     */
    private $referenceReturnMethods;
    /**
     * @var array
     */
    private $nonReferenceReturnMethods;

    /**
     * @param array $referenceReturnMethods
     * @param array $nonReferenceReturnMethods
     */
    public function __construct(array $referenceReturnMethods, array $nonReferenceReturnMethods)
    {
        $this->referenceReturnMethods = $referenceReturnMethods;
        $this->nonReferenceReturnMethods = $nonReferenceReturnMethods;
    }

    /**
     * @return array
     */
    public function getReferenceReturnMethods()
    {
        return $this->referenceReturnMethods;
    }

    /**
     * @return array
     */
    public function getNonReferenceReturnMethods()
    {
        return $this->nonReferenceReturnMethods;
    }

    public function addReferenceReturnMethod($methodName)
    {
        if (false === isset($this->referenceReturnMethods[$methodName])) {
            $this->referenceReturnMethods[$methodName] = 0;
        }
        ++$this->referenceReturnMethods[$methodName];
    }

    public function addNonReferenceReturnMethod($methodName)
    {
        if (false === isset($this->nonReferenceReturnMethods[$methodName])) {
            $this->nonReferenceReturnMethods[$methodName] = 0;
        }
        ++$this->nonReferenceReturnMethods[$methodName];
    }
}
