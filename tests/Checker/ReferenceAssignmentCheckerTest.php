<?php


namespace umulmrum\PhpReferenceChecker\Checker;


use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use umulmrum\PhpReferenceChecker\DataModel\NonReferenceAssignmentWarning;

class ReferenceAssignmentCheckerTest extends TestCase
{
    /**
     * @var ReferenceAssignmentChecker
     */
    private $checker;
    /**
     * @var array
     */
    private $actualResult;

    protected function tearDown()
    {
        parent::tearDown();

        $this->checker = null;
        $this->actualResult = null;
    }

    public function testNestingLevel()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/FileAndFolder/ReferenceAssignmentCheckerFixture.php',
            __DIR__.'/../fixtures/FileAndFolder'
        );
        $this->thenTheMaxSettingLevelShouldBeFalseOrAt(3000);
    }

    public function testCheckOnOneFile()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__ . '/../fixtures/FileAndFolder/ReferenceAssignmentCheckerFixture.php',
            __DIR__ . '/../fixtures/FileAndFolder'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            'ReferenceAssignmentCheckerFixture.php',
            13,
            1.0
        );
    }

    public function testCheckOnDirectory()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__ . '/../fixtures/FileAndFolder',
            __DIR__ . '/../fixtures/FileAndFolder'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            'ReferenceAssignmentCheckerFixture.php',
            13,
            1.0
        );
    }

    public function testFiftyPercent()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/Certainty/50Percent.php',
            __DIR__.'/../fixtures/Certainty/50Percent.php'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            '50Percent.php',
            23,
            0.5
        );
    }

    public function testTwentyFivePercent()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/Certainty/25Percent.php',
            __DIR__.'/../fixtures/Certainty/25Percent.php'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            '25Percent.php',
            37,
            0.25
        );
    }

    public function testSure()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/Certainty/Sure.php',
            __DIR__.'/../fixtures/Certainty/Sure.php'
        );
        $this->thenIShouldReceiveNoWarning();
    }

    private function givenAReferenceAssignmentChecker()
    {
        $this->checker = new ReferenceAssignmentChecker(new NullLogger());
    }

    private function whenICallCheckOn($checkFilePath, $repoPath)
    {
        $this->actualResult = $this->checker->check($checkFilePath, $repoPath);
    }

    private function thenIShouldReceiveANonReferenceAssignmentWarningWith($file, $line, $certainty)
    {
        static::assertEquals(
            [
                new NonReferenceAssignmentWarning($file, $line, $certainty)
            ],
            $this->actualResult);
    }

    private function thenTheMaxSettingLevelShouldBeFalseOrAt($level)
    {
        $expected = [$level, false];
        static::assertContains(ini_get('xdebug.max_nesting_level'), $expected);
    }

    private function thenIShouldReceiveNoWarning()
    {
        static::assertEmpty($this->actualResult);
    }
}