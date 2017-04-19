<?php

namespace umulmrum\PhpReferenceChecker\Checker;

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

    /**
     * {@inheritdoc}
     */
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
            include __DIR__.'/../fixtures/FileAndFolder/ReferenceAssignmentCheckerRepository.php'
        );
        $this->thenTheMaxSettingLevelShouldBeFalseOrAt(3000);
    }

    public function testCheckOnOneFile()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/FileAndFolder/ReferenceAssignmentCheckerFixture.php',
            include __DIR__.'/../fixtures/FileAndFolder/ReferenceAssignmentCheckerRepository.php'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            '/ReferenceAssignmentCheckerFixture.php',
            13,
            1.0
        );
    }

    public function testCaseInsensitiveCheck()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/CaseSensitivity/CaseSensitivityFixture.php',
            include __DIR__.'/../fixtures/CaseSensitivity/CaseSensitivityRepository.php'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            '/CaseSensitivityFixture.php',
            13,
            1.0
        );
    }

    public function testCheckOnDirectory()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/FileAndFolder',
            include __DIR__.'/../fixtures/FileAndFolder/ReferenceAssignmentCheckerRepository.php'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            '/ReferenceAssignmentCheckerFixture.php',
            13,
            1.0
        );
    }

    public function testSubDirInWarning()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/Path',
            include __DIR__.'/../fixtures/Path/Subdir/SubPathRepository.php'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            '/Subdir/SubPathFixture.php',
            13,
            1.0
        );
    }

    public function testFiftyPercent()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/Certainty/50Percent.php',
            include __DIR__.'/../fixtures/Certainty/50PercentRepository.php'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            '/50Percent.php',
            27,
            0.5
        );
    }

    public function testTwentyFivePercent()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/Certainty/25Percent.php',
            include __DIR__.'/../fixtures/Certainty/25PercentRepository.php'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            '/25Percent.php',
            43,
            0.25
        );
    }

    public function testSure()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/Certainty/Sure.php',
            include __DIR__.'/../fixtures/Certainty/SureRepository.php'
        );
        $this->thenIShouldReceiveNoWarning();
    }

    public function testBlocks()
    {
        $this->givenAReferenceAssignmentChecker();
        $this->whenICallCheckOn(
            __DIR__.'/../fixtures/Blocks/Blocks.php',
            include __DIR__.'/../fixtures/Blocks/BlocksRepository.php'
        );
        $this->thenIShouldReceiveANonReferenceAssignmentWarningWith(
            '/Blocks.php',
            19,
            1.0
        );
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
                new NonReferenceAssignmentWarning($file, $line, $certainty),
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
