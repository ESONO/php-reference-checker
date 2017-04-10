<?php

namespace umulmrum\PhpReferenceChecker\Runner;

use umulmrum\PhpReferenceChecker\Checker\ReferenceAssignmentChecker;
use umulmrum\PhpReferenceChecker\DataModel\NonReferenceAssignmentWarning;
use umulmrum\PhpReferenceChecker\Repository\MethodRepositoryBuilder;

class Runner
{
    /**
     * @var MethodRepositoryBuilder
     */
    private $methodRepositoryBuilder;
    /**
     * @var ReferenceAssignmentChecker
     */
    private $referenceAssignmentChecker;

    /**
     * @param MethodRepositoryBuilder    $methodRepositoryBuilder
     * @param ReferenceAssignmentChecker $referenceAssignmentChecker
     */
    public function __construct(
        MethodRepositoryBuilder $methodRepositoryBuilder,
        ReferenceAssignmentChecker $referenceAssignmentChecker)
    {
        $this->methodRepositoryBuilder = $methodRepositoryBuilder;
        $this->referenceAssignmentChecker = $referenceAssignmentChecker;
    }

    /**
     * @param string $targetPath
     * @param string $classRepositoryPath
     *
     * @return NonReferenceAssignmentWarning[]
     */
    public function runCheck($targetPath, $classRepositoryPath)
    {
        $methodRepository = $this->methodRepositoryBuilder->build($classRepositoryPath);

        return $this->referenceAssignmentChecker->check($targetPath, $methodRepository);
    }
}
