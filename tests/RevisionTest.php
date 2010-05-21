<?php

class MergeHelper_RevisionTest extends PHPUnit_Framework_TestCase {

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
	
}
