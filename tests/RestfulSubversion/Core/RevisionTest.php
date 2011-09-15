<?php

class RestfulSubversion_Core_RevisionTest extends PHPUnit_Framework_TestCase
{
    public function test_setAndGetRevisionNumber()
    {
        $revision = new RestfulSubversion_Core_Revision('12345');
        $this->assertSame('12345', $revision->getAsString());
    }

    public function test_setAndGetRevisionHead()
    {
        $revision = new RestfulSubversion_Core_Revision('HEAD');
        $this->assertSame('HEAD', $revision->getAsString());
    }

    /**
     * @expectedException RestfulSubversion_Core_RevisionInvalidRevisionNumberCoreException
     */
    public function test_invalidNumbersThrowExceptionFloat()
    {
        $revision = new RestfulSubversion_Core_Revision(1.3234);
    }

    /**
     * @expectedException RestfulSubversion_Core_RevisionInvalidRevisionNumberCoreException
     */
    public function test_invalidNumbersThrowExceptionArbitraryString()
    {
        $revision = new RestfulSubversion_Core_Revision('Hello World');
    }

    /**
     * @expectedException RestfulSubversion_Core_RevisionInvalidRevisionNumberCoreException
     */
    public function test_invalidNumbersThrowExceptionNegative()
    {
        $revision = new RestfulSubversion_Core_Revision('-12345');
    }

    public function test_getRevertedAstring()
    {
        $revision = new RestfulSubversion_Core_Revision('12345');
        $this->assertSame('-12345', $revision->sGetRevertedAstring());
    }

    public function test_getAsString()
    {
        $revision = new RestfulSubversion_Core_Revision('12345');
        $this->assertSame('12345', $revision->getAsString());
    }

    public function test_toString()
    {
        $revision = new RestfulSubversion_Core_Revision('12345');
        $this->assertSame('12345', "$revision");
    }
}
