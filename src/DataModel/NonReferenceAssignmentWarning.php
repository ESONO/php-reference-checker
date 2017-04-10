<?php

namespace umulmrum\PhpReferenceChecker\DataModel;

class NonReferenceAssignmentWarning
{
    /**
     * @var string
     */
    private $file;
    /**
     * @var int
     */
    private $line;
    /**
     * @var int
     */
    private $probability;

    /**
     * @param string $file
     * @param int    $line
     * @param int    $probability
     */
    public function __construct($file, $line, $probability)
    {
        $this->file = $file;
        $this->line = $line;
        $this->probability = $probability;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getProbability()
    {
        return $this->probability;
    }
}
