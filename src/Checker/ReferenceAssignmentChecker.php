<?php


namespace umulmrum\PhpReferenceChecker\Checker;


use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use umulmrum\PhpReferenceChecker\DataModel\MethodRepository;
use umulmrum\PhpReferenceChecker\DataModel\NonReferenceAssignmentWarning;

class ReferenceAssignmentChecker
{
    /**
     * @param string $targetPath
     * @param string $classRepositoryPath
     *
     * @return NonReferenceAssignmentWarning[]
     */
    public function check($targetPath, $classRepositoryPath)
    {
        $this->init();
        $repo = $this->buildMethodRepository($classRepositoryPath);
        $warnings = $this->checkTargetPath($targetPath, $repo);

        return $warnings;
    }

    private function init()
    {
        ini_set('xdebug.max_nesting_level', 3000);
    }

    /**
     * @param string           $targetPath
     * @param MethodRepository $repository
     *
     * @return NonReferenceAssignmentWarning[]
     */
    private function checkTargetPath($targetPath, MethodRepository $repository)
    {
        if (is_file($targetPath)) {
            return $this->checkFile($targetPath, $repository);
        }
        $finder = new Finder();

        $warnings = [];
        $finder
            ->in($targetPath)
            ->files()
            ->name('*.php');

        foreach ($finder as $file) {
            $warnings = array_merge($warnings, $this->checkFile($file->getPathname(), $repository));
        }

        return $warnings;
    }

    private function checkFile($path, MethodRepository $repository)
    {
        $warnings = [];
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $nodes = $parser->parse(file_get_contents($path));
        foreach ($nodes as $node) {
            if (false === $node instanceof Class_) {
                continue;
            }
            /**
             * @var Class_ $node
             */
            $methods = $node->getMethods();
            foreach ($methods as $method) {
                $stmts = $method->getStmts();
                foreach ($stmts as $stmt) {
                    if (false === $stmt instanceof AssignRef) {
                        continue;
                    }
                    /**
                     * @var AssignRef $stmt
                     */
                    $expr = $stmt->expr;
                    if (false === $expr instanceof MethodCall) {
                        continue;
                    }
                    /**
                     * @var MethodCall $expr
                     */
                    $name = $expr->name;
                    $nonReferenceReturns = isset($repository->getNonReferenceReturnMethods()[$name]) ?: 0;
                    $referenceReturns = isset($repository->getReferenceReturnMethods()[$name]) ?: 0;

                    if ($nonReferenceReturns > 0 && $referenceReturns === 0) {
                        // we know it is not ok!
                        $warnings[] = new NonReferenceAssignmentWarning(basename($path), $expr->getLine(), 1.0);
                    }
                }
            }
        }


        return $warnings;

    }

    /**
     * @param $classRepositoryPath
     *
     * @return MethodRepository
     */
    private function buildMethodRepository($classRepositoryPath)
    {
        if (is_file($classRepositoryPath)) {
            return $this->addToRepository($classRepositoryPath, new MethodRepository([], []));
        }
        $finder = new Finder();
        $finder
            ->in($classRepositoryPath)
            ->files()
            ->name('*.php');


        $repo = new MethodRepository([], []);
        foreach ($finder as $file) {
            $repo = $this->addToRepository($file->getPathname(), $repo);
        }

        return $repo;
    }

    private function addToRepository($filePath, MethodRepository $repository)
    {

        $referenceReturnMethods = $repository->getReferenceReturnMethods();
        $nonReferenceReturnMethods = $repository->getNonReferenceReturnMethods();
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $nodes = $parser->parse(file_get_contents($filePath));
        foreach ($nodes as $node) {
            if (false === $node instanceof Class_) {
                continue; // TODO check functions defined outside of classes
            }
            /**
             * @var Class_ $node
             */
            foreach ($node->getMethods() as $method) {
                if (true === $method->byRef) {
                    if (false === isset($referenceReturnMethods[$method->name])) {
                        $referenceReturnMethods[$method->name] = 0;
                    }
                    $referenceReturnMethods[$method->name]++;
                } else {
                    if (false === isset($nonReferenceReturnMethods[$method->name])) {
                        $nonReferenceReturnMethods[$method->name] = 0;
                    }
                    $nonReferenceReturnMethods[$method->name]++;
                }
            }
        }

        return new MethodRepository($referenceReturnMethods, $nonReferenceReturnMethods);
    }
}