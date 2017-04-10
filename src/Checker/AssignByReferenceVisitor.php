<?php

namespace umulmrum\PhpReferenceChecker\Checker;

use PhpParser\Node;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeVisitorAbstract;
use umulmrum\PhpReferenceChecker\DataModel\MethodRepository;
use umulmrum\PhpReferenceChecker\DataModel\NonReferenceAssignmentWarning;

class AssignByReferenceVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $warnings;
    /**
     * @var MethodRepository
     */
    private $repository;
    /**
     * @var string
     */
    private $path;

    /**
     * @param array            $warnings
     * @param MethodRepository $repository
     * @param string           $path
     */
    public function __construct(array &$warnings, MethodRepository $repository, $path)
    {
        $this->warnings = &$warnings;
        $this->repository = $repository;
        $this->path = $path;
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        if (false === $node instanceof AssignRef) {
            return null;
        }
        /**
         * @var AssignRef $node
         */
        $expr = $node->expr;
        if (false === $expr instanceof MethodCall) {
            return null;
        }
        /**
         * @var MethodCall $expr
         */
        $name = $expr->name;
        $nonReferenceReturns = isset($this->repository->getNonReferenceReturnMethods()[$name]) ? $this->repository->getNonReferenceReturnMethods()[$name] : 0;
        $referenceReturns = isset($this->repository->getReferenceReturnMethods()[$name]) ? $this->repository->getReferenceReturnMethods()[$name] : 0;

        if ($referenceReturns === 0 && $nonReferenceReturns > 0) {
            // we know it is not ok!
            $this->warnings[] = new NonReferenceAssignmentWarning(basename($this->path), $expr->getLine(), 1.0);

            return null;
        }
        if ($referenceReturns > 0 && $nonReferenceReturns > 0) {
            // this may be ok
            $this->warnings[] = new NonReferenceAssignmentWarning(
                basename($this->path),
                $expr->getLine(),
                $referenceReturns / ($nonReferenceReturns + $referenceReturns)
            );

            return null;
        }
        // todo: handle unknown method names

        return null;
    }
}
