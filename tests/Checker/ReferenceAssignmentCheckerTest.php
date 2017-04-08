<?php


namespace umulmrum\PhpReferenceChecker\Checker;


use PHPUnit\Framework\TestCase;
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
        $this->whenICallCheckOn(__DIR__ . '/../fixtures/ReferenceAssignmentCheckerFixture.php');
        $this->thenTheMaxSettingLevelShouldBeFalseOrAt(3000);
    }

    public function testCheckOnOneFile()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(__DIR__ . '/../fixtures/ReferenceAssignmentCheckerFixture.php');
        $this->thenIShouldReceiveANonReferenceAssignmentWarning();
    }

    public function testCheckOnDirectory()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(__DIR__ . '/../fixtures');
        $this->thenIShouldReceiveANonReferenceAssignmentWarning();
    }

    private function givenAReferenceAssignmentChecker()
    {
        $this->checker = new ReferenceAssignmentChecker();
    }

    private function whenICallCheckOn($checkFilePath)
    {
        $this->actualResult = $this->checker->check($checkFilePath, __DIR__ . '/../fixtures/');
    }

    private function thenIShouldReceiveANonReferenceAssignmentWarning()
    {
        static::assertEquals(
            [
                new NonReferenceAssignmentWarning('ReferenceAssignmentCheckerFixture.php', 13, 1.0)
            ],
            $this->actualResult);
    }

    private function thenTheMaxSettingLevelShouldBeFalseOrAt($level)
    {
        $expected = [$level, false];
        static::assertContains(ini_get('xdebug.max_nesting_level'), $expected);
    }
}