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
        $this->whenICallCheck();
        $this->thenTheMaxSettingLevelShouldBeFalseOrAt(3000);
    }

    public function testCheck()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheck();
        $this->thenIShouldReceiveANonReferenceAssignmentWarning();
    }

    private function givenAReferenceAssignmentChecker()
    {
        $this->checker = new ReferenceAssignmentChecker();
    }

    private function whenICallCheck()
    {
        $this->actualResult = $this->checker->check(__DIR__ . '/../fixtures/ReferenceAssignmentCheckerFixture.php', __DIR__ . '/../fixtures/');
    }

    private function thenIShouldReceiveANonReferenceAssignmentWarning()
    {
        static::assertEquals(
            [
                new NonReferenceAssignmentWarning('ReferenceAssignmentCheckerFixture.php', 13)
            ],
            $this->actualResult);
    }

    private function thenTheMaxSettingLevelShouldBeFalseOrAt($level)
    {
        $expected = [$level, false];
        static::assertContains(ini_get('xdebug.max_nesting_level'), $expected);
    }
}