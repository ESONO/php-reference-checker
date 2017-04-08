<?php


namespace umulmrum\PhpReferenceChecker\Checker;


use PhpParser\Error;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\ParserFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use umulmrum\PhpReferenceChecker\DataModel\MethodRepository;
use umulmrum\PhpReferenceChecker\DataModel\NonReferenceAssignmentWarning;

class ReferenceAssignmentChecker
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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

        return $this->checkTargetPath($targetPath, $repo);
    }

    private function init()
    {
        $this->logger->info('init: start');
        ini_set('xdebug.max_nesting_level', 3000);
        $this->logger->info('init: end');
    }

    /**
     * @param string           $targetPath
     * @param MethodRepository $repository
     *
     * @return NonReferenceAssignmentWarning[]
     */
    private function checkTargetPath($targetPath, MethodRepository $repository)
    {
        $this->logger->info('checkTargetPath: start');
        if (is_file($targetPath)) {
            $this->logger->info('checkTargetPath: end');

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
        $this->logger->info('checkTargetPath: end');

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
                if (!is_iterable($stmts)) {
                    continue;
                }
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
                    $nonReferenceReturns = isset($repository->getNonReferenceReturnMethods()[$name]) ? $repository->getNonReferenceReturnMethods()[$name] : 0;
                    $referenceReturns = isset($repository->getReferenceReturnMethods()[$name]) ? $repository->getReferenceReturnMethods()[$name] : 0;

                    if ($referenceReturns === 0 && $nonReferenceReturns > 0) {
                        // we know it is not ok!
                        $warnings[] = new NonReferenceAssignmentWarning(basename($path), $expr->getLine(), 1.0);
                        continue;
                    }
                    if ($referenceReturns > 0 && $nonReferenceReturns > 0) {
                        // this may be ok
                        $warnings[] = new NonReferenceAssignmentWarning(
                            basename($path),
                            $expr->getLine(),
                            $referenceReturns / ($nonReferenceReturns + $referenceReturns)
                        );
                        continue;
                    }
                    // todo: handle unknown method names
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
        $this->logger->info('buildMethodRepository: start');
        if (is_file($classRepositoryPath)) {
            $this->logger->info('buildMethodRepository: end');
            $repo = new MethodRepository([], []);

            $this->addToRepository($classRepositoryPath, $repo);

            return $repo;
        }
        $finder = new Finder();
        $finder
            ->in($classRepositoryPath)
            ->files()
            ->name('*.php');


        $repo = new MethodRepository([], []);
        foreach ($finder as $file) {
            $this->addToRepository($file->getPathname(), $repo);
        }
        $this->logger->info('buildMethodRepository: end');

        return $repo;
    }

    /**
     * @param $filePath
     * @param MethodRepository $repository
     */
    private function addToRepository($filePath, MethodRepository $repository)
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        try {
            $nodes = $parser->parse(file_get_contents($filePath));
        } catch (Error $e) {
            $this->logger->warning($filePath . ': ' . $e->getMessage());
            return;
        }

        foreach ($nodes as $node) {
            if (false === $node instanceof Class_) {
                continue; // TODO check functions defined outside of classes
            }
            /**
             * @var Class_ $node
             */
            foreach ($node->getMethods() as $method) {
                if (true === $method->byRef) {
                    $repository->addReferenceReturnMethod($method->name);
                } else {
                    $repository->addNonReferenceReturnMethod($method->name);
                }
            }
        }
    }
}