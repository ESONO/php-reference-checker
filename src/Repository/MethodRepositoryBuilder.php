<?php
namespace umulmrum\PhpReferenceChecker\Repository;

use umulmrum\PhpReferenceChecker\DataModel\MethodRepository;

interface MethodRepositoryBuilder
{
    /**
     * @param string $path
     *
     * @return MethodRepository
     */
    public function build($path);
}