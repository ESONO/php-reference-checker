<?php

namespace umulmrum\PhpReferenceChecker\Repository;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use umulmrum\PhpReferenceChecker\DataModel\MethodRepository;

class MethodRepositoryBuilderImplTest extends TestCase
{
    /**
     * @var MethodRepositoryBuilderImpl
     */
    private $methodRepositoryBuilderImpl;
    /**
     * @var MethodRepository
     */
    private $actualResult;

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->methodRepositoryBuilderImpl = null;
        $this->actualResult = null;
    }

    /**
     * @param string           $path
     * @param MethodRepository $expectedResult
     *
     * @dataProvider getBuildData
     */
    public function testBuild($path, $expectedResult)
    {
        $this->givenAMethodRepositoryBuilderImpl();
        $this->whenICallBuild($path);
        $this->thenTheExpectedRepositoryShouldBeReturned($expectedResult);
    }

    private function givenAMethodRepositoryBuilderImpl()
    {
        $this->methodRepositoryBuilderImpl = new MethodRepositoryBuilderImpl(new NullLogger());
    }

    private function whenICallBuild($path)
    {
        $this->actualResult = $this->methodRepositoryBuilderImpl->build($path);
    }

    private function thenTheExpectedRepositoryShouldBeReturned($expectedResult)
    {
        static::assertEquals($expectedResult, $this->actualResult);
    }

    public function getBuildData()
    {
        return [
            [
                __DIR__.'/../fixtures/FileAndFolder/ReferenceAssignmentCheckerFixture.php',
                include __DIR__.'/../fixtures/FileAndFolder/ReferenceAssignmentCheckerRepository.php',
            ],
            [
                __DIR__.'/../fixtures/Certainty/50Percent.php',
                include __DIR__.'/../fixtures/Certainty/50PercentRepository.php',
            ],
            [
                __DIR__.'/../fixtures/Certainty/25Percent.php',
                include __DIR__.'/../fixtures/Certainty/25PercentRepository.php',
            ],
            [
                __DIR__.'/../fixtures/Certainty/Sure.php',
                include __DIR__.'/../fixtures/Certainty/SureRepository.php',
            ],
            [
                __DIR__.'/../fixtures/Functions/Functions.php',
                include __DIR__.'/../fixtures/Functions/FunctionsRepository.php',
            ],
            [
                __DIR__.'/../fixtures/Static/Static.php',
                include __DIR__.'/../fixtures/Static/StaticRepository.php',
            ],
            [
                __DIR__.'/../fixtures/Closure/Closure.php',
                include __DIR__.'/../fixtures/Closure/ClosureRepository.php',
            ],
        ];
    }

    public function testIgnoreBrokenSymlinks()
    {
        $this->givenAMethodRepositoryBuilderImpl();
        $this->whenICallBuildOnABrokenSymlink();
        $this->thenAnEmptyResultShouldBeReturned();
    }

    private function whenICallBuildOnABrokenSymlink()
    {
        $this->actualResult = $this->methodRepositoryBuilderImpl->build(__DIR__.'/../fixtures/Symlink/BrokenSymlink.php');
    }

    private function thenAnEmptyResultShouldBeReturned()
    {
        static::assertEquals(new MethodRepository([], []), $this->actualResult);
    }

    public function testIgnoreBrokenSymlinksInDeepPath()
    {
        $path = __DIR__.'/../fixtures/Symlink';
        $this->givenAMethodRepositoryBuilderImpl();
        $this->whenICallBuild($path);
        $this->thenTheIndexWithoutSymlinkShouldBeBuilt($path);
    }

    private function thenTheIndexWithoutSymlinkShouldBeBuilt($path)
    {
        static::assertEquals(include $path.'/SomeValidClassRepository.php', $this->actualResult);
    }
}
