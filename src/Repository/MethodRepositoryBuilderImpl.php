<?php

namespace umulmrum\PhpReferenceChecker\Repository;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use umulmrum\PhpReferenceChecker\DataModel\MethodRepository;

class MethodRepositoryBuilderImpl implements MethodRepositoryBuilder
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $path
     *
     * @return MethodRepository
     */
    public function build($path)
    {
        $this->logger->info('build: start');
        $repo = new MethodRepository([], []);
        if (false === file_exists($path)) {
            $this->logger->warning('Path not found: '.$path);

            return $repo;
        }
        if (is_file($path)) {
            $this->logger->info('build: end');
            $this->addToRepository($path, $repo);

            return $repo;
        }
        $finder = new Finder();
        $finder
            ->in($path)
            ->files()
            ->name('*.php');

        foreach ($finder as $file) {
            if (file_exists($file)) {
                $this->addToRepository($file->getPathname(), $repo);
            }
        }
        $this->logger->info('buildMethodRepository: end');

        return $repo;
    }

    /**
     * @param string           $filePath
     * @param MethodRepository $repository
     */
    private function addToRepository($filePath, MethodRepository $repository)
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        try {
            $nodes = $parser->parse(file_get_contents($filePath));
        } catch (Error $e) {
            $this->logger->warning($filePath.': '.$e->getMessage());

            return;
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new RepositoryBuilderNodeVisitor($repository));
        $traverser->traverse($nodes);
    }
}
