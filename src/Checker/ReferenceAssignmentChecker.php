<?php


namespace umulmrum\PhpReferenceChecker\Checker;


use PhpParser\Node\Stmt\Class_;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;

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
        $this->buildMethodRepository($classRepositoryPath);

        return [];
    }

    private function init()
    {
        ini_set('xdebug.max_nesting_level', 3000);
    }

    private function buildMethodRepository($classRepositoryPath)
    {
        $repo = [];
        $finder = new Finder();
        $finder
            ->in($classRepositoryPath)
            ->files()
            ->name('*.php')
        ;
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        $referenceReturnMethods = [];
        $nonReferenceReturnMethods = [];

        foreach ($finder as $file) {
            $nodes = $parser->parse(file_get_contents($file->getPathname()));
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
        }

        return $repo;
    }
}