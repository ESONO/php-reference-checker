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
     * @param string $file
     * @param int $line
     */
    public function __construct($file, $line)
    {
        $this->file = $file;
        $this->line = $line;
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
}