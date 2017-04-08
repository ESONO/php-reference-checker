<?php


namespace umulmrum\PhpReferenceChecker\Checker;


class ReferenceAssignmentChecker
{
    /**
     * @param string $targetPath
     * @param string $classRepositoryPath
     *
     * @return array
     */
    public function check($targetPath, $classRepositoryPath)
    {
        $this->init();
        return [];
    }

    private function init()
    {
        ini_set('xdebug.max_nesting_level', 3000);
    }
}