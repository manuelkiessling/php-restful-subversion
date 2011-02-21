<?php

class MergeHelper_RevisionTest extends PHPUnit_Framework_TestCase {

	public function test_setAndGetRevision() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame('12345', $oRevision->sGetNumber());
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
	public function test_invalidNumbersThrowExceptionNegative() {
		$oRevision = new MergeHelper_Revision('-12345');
	}

	public function test_setAndGetRevisionReverted() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame('-12345', $oRevision->sGetNumberInverted());
	}

	public function test_toString() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame($oRevision->sGetNumber(), "$oRevision");
	}
	
}
