<?php

class MergeHelper_RevisionTest extends PHPUnit_Framework_TestCase {

	/**
     * @expectedException MergeHelper_RevisionInvalidRevisionNumberException
     */
	public function test_invalidNumbersThrowExceptionsInavlidSyntaxInBegin() {
		$oRevision = new MergeHelper_Revision('12345:3242');
	}

	/**
     * @expectedException MergeHelper_RevisionInvalidRevisionNumberException
     */
	public function test_invalidNumbersThrowExceptionsInavlidSyntaxInEnd() {
		$oRevision = new MergeHelper_Revision('12345', 'r23234');
	}
	
	/**
     * @expectedException MergeHelper_RevisionInvalidRevisionNumberException
     */
	public function test_invalidNumbersThrowExceptionFloatInBegin() {
		$oRevision = new MergeHelper_Revision(1.3234);
	}

	/**
     * @expectedException MergeHelper_RevisionInvalidRevisionNumberException
     */
	public function test_invalidNumbersThrowExceptionFloatInEnd() {
		$oRevision = new MergeHelper_Revision('12345', 1.3234);
	}

	/**
     * @expectedException MergeHelper_RevisionInvalidRevisionNumberException
     */
	public function test_invalidNumbersThrowExceptionNegativeNumbersInRangeForBegin() {
		$oRevision = new MergeHelper_Revision('-43278', '-3288');
	}

	/**
     * @expectedException MergeHelper_RevisionInvalidRevisionNumberException
     */
	public function test_invalidNumbersThrowExceptionNegativeNumbersInRangeForEnd() {
		$oRevision = new MergeHelper_Revision('43278', '-3288');
	}

	public function test_setAndGetSingleRevision() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame('12345', $oRevision->sGetNumber());
	}

	public function test_setAndGetSingleRevisionReverted() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame('-12345', $oRevision->sGetNumberInverted());
	}

	public function test_singleRevisionIsNotRange() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertFalse($oRevision->bIsRange());
	}

	public function test_toStringForSingleRevision() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame($oRevision->sGetNumber(), "$oRevision");
	}

	public function test_setAndGetRevisionRange() {
		$oRevision = new MergeHelper_Revision('12345', '12349');
		$this->assertSame('12345:12349', $oRevision->sGetNumber());
	}

	public function test_setAndGetRevisionRangeReverted() {
		$oRevision = new MergeHelper_Revision('12345', '12349');
		$this->assertSame('12349:12345', $oRevision->sGetNumberInverted());
	}

	public function test_revisionRangeGetNumberBegin() {
		$oRevision = new MergeHelper_Revision('12345', '12349');
		$this->assertSame('12345', $oRevision->sGetNumberBegin());
	}

	public function test_revisionRangeGetNumberEnd() {
		$oRevision = new MergeHelper_Revision('12345', '12349');
		$this->assertSame('12349', $oRevision->sGetNumberEnd());
	}

	public function test_revisionRangeIsRange() {
		$oRevision = new MergeHelper_Revision('12345', '12349');
		$this->assertTrue($oRevision->bIsRange());
	}

	public function test_toStringForRevisionRange() {
		$oRevision = new MergeHelper_Revision('12345', '12349');
		$this->assertSame($oRevision->sGetNumber(), "$oRevision");
	}

	public function test_getNumberInvertedSinglePositiveToNegative() {
		$oRevision = new MergeHelper_Revision('12345');

		$this->assertSame('-12345', $oRevision->sGetNumberInverted());
	}
	
	public function test_getNumberInvertedSingleNegativeToPositive() {
		$oRevision = new MergeHelper_Revision('-12345');

		$this->assertSame('12345', $oRevision->sGetNumberInverted());
	}

	public function test_getNumberInvertedRangePositiveToNegative() {
		$oRevision = new MergeHelper_Revision('12345', '56789');

		$this->assertSame('56789:12345', $oRevision->sGetNumberInverted());
	}
	
	public function test_getNumberInvertedRangeNegativeToPositive() {
		$oRevision = new MergeHelper_Revision('56789', '12345');

		$this->assertSame('12345:56789', $oRevision->sGetNumberInverted());
	}

	public function test_getRevertedRevisionAsObjectForSingle() {
		$oRevision = new MergeHelper_Revision('12345');
		$oRevisionReverted = new MergeHelper_Revision('-12345');

		$this->assertEquals($oRevisionReverted, $oRevision->getRevertedRevisionAsObject());
	}

	public function test_getRevertedRevisionAsObjectForRange() {
		$oRevision = new MergeHelper_Revision('12345', '56789');
		$oRevisionReverted = new MergeHelper_Revision('56789', '12345');

		$this->assertEquals($oRevisionReverted, $oRevision->getRevertedRevisionAsObject());
	}

}
