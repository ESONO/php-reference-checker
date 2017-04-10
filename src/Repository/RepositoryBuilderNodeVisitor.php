<?php

namespace umulmrum\PhpReferenceChecker\Repository;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeVisitorAbstract;
use umulmrum\PhpReferenceChecker\DataModel\MethodRepository;

class RepositoryBuilderNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var MethodRepository
     */
    private $repository;

    /**
     * @param MethodRepository $repository
     */
    public function __construct(MethodRepository $repository)
    {
        $this->repository = $repository;
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        if (false === $node instanceof FunctionLike) {
            return null;
        }

        /**
         * @var Closure|Function_|ClassMethod $node
         */
        if (true === $node->returnsByRef()) {
            $this->repository->addReferenceReturnMethod($node->name);
        } else {
            $this->repository->addNonReferenceReturnMethod($node->name);
        }
    }
}
