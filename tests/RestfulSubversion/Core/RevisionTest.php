<?php

class RestfulSubversion_Core_RevisionTest extends PHPUnit_Framework_TestCase {

	public function test_setAndGetRevisionNumber() {
		$oRevision = new RestfulSubversion_Core_Revision('12345');
		$this->assertSame('12345', $oRevision->sGetAsString());
	}

	public function test_setAndGetRevisionHead() {
		$oRevision = new RestfulSubversion_Core_Revision('HEAD');
		$this->assertSame('HEAD', $oRevision->sGetAsString());
	}

	/**
     * @expectedException RestfulSubversion_Core_RevisionInvalidRevisionNumberCoreException
     */
	public function test_invalidNumbersThrowExceptionFloat() {
		$oRevision = new RestfulSubversion_Core_Revision(1.3234);
	}

	/**
     * @expectedException RestfulSubversion_Core_RevisionInvalidRevisionNumberCoreException
     */
	public function test_invalidNumbersThrowExceptionArbitraryString() {
		$oRevision = new RestfulSubversion_Core_Revision('Hello World');
	}

	/**
     * @expectedException RestfulSubversion_Core_RevisionInvalidRevisionNumberCoreException
     */
	public function test_invalidNumbersThrowExceptionNegative() {
		$oRevision = new RestfulSubversion_Core_Revision('-12345');
	}

	public function test_getRevertedAsString() {
		$oRevision = new RestfulSubversion_Core_Revision('12345');
		$this->assertSame('-12345', $oRevision->sGetRevertedAsString());
	}

	public function test_getAsString() {
		$oRevision = new RestfulSubversion_Core_Revision('12345');
		$this->assertSame('12345', $oRevision->sGetAsString());
	}

	public function test_toString() {
		$oRevision = new RestfulSubversion_Core_Revision('12345');
		$this->assertSame('12345', "$oRevision");
	}
	
}
