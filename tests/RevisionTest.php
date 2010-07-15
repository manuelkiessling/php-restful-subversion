<?php

class MergeHelper_RevisionTest extends PHPUnit_Framework_TestCase {

	/**
     * @expectedException MergeHelper_RevisionInvalidRevisionNumberException
     */
	public function test_invalidNumbersThrowExceptionRangeAsString() {
		$oRevision = new MergeHelper_Revision('12345:3242');
	}
	
	/**
     * @expectedException MergeHelper_RevisionInvalidRevisionNumberException
     */
	public function test_invalidNumbersThrowExceptionFloat() {
		$oRevision = new MergeHelper_Revision(1.3234);
	}

	/**
     * @expectedException MergeHelper_RevisionInvalidRevisionNumberException
     */
	public function test_invalidNumbersThrowExceptionNegativeNumbersInRange() {
		$oRevision = new MergeHelper_Revision('-43278', '-3288');
	}

	public function test_setAndGetSingle() {
		
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame('12345', "$oRevision");
		$this->assertSame('12345', $oRevision->sGetNumber());
		$this->assertSame('-12345', $oRevision->sGetNumberInverted());
		$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $oRevision->sGetNumber());
		$this->assertFalse($oRevision->bIsRange());
		
	}

	public function test_setAndGetRange() {
		
		$oRevision = new MergeHelper_Revision('12345', '12349');
		$this->assertSame('12345:12349', "$oRevision");
		$this->assertSame('12345:12349', $oRevision->sGetNumber());
		$this->assertSame('12349:12345', $oRevision->sGetNumberInverted());
		$this->assertSame('12345', $oRevision->sGetNumberBegin());
		$this->assertSame('12349', $oRevision->sGetNumberEnd());
		$this->assertTrue($oRevision->bIsRange());
		
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
