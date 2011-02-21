<?php

class MergeHelper_RevisionTest extends PHPUnit_Framework_TestCase {

	public function test_setAndGetRevisionNumber() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame('12345', $oRevision->sGetAsString());
	}

	public function test_setAndGetRevisionHead() {
		$oRevision = new MergeHelper_Revision('HEAD');
		$this->assertSame('HEAD', $oRevision->sGetAsString());
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
	public function test_invalidNumbersThrowExceptionArbitraryString() {
		$oRevision = new MergeHelper_Revision('Hello World');
	}

	/**
     * @expectedException MergeHelper_RevisionInvalidRevisionNumberException
     */
	public function test_invalidNumbersThrowExceptionNegative() {
		$oRevision = new MergeHelper_Revision('-12345');
	}

	public function test_getRevertedAsString() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame('-12345', $oRevision->sGetRevertedAsString());
	}

	public function test_getAsString() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame('12345', $oRevision->sGetAsString());
	}

	public function test_toString() {
		$oRevision = new MergeHelper_Revision('12345');
		$this->assertSame('12345', "$oRevision");
	}
	
}
