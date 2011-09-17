<?php

namespace RestfulSubversion\Core;

class RevisionTest extends \PHPUnit_Framework_TestCase
{
    public function test_setAndGetRevisionNumber()
    {
        $revision = new Revision('12345');
        $this->assertSame('12345', $revision->getAsString());
    }

    public function test_setAndGetRevisionHead()
    {
        $revision = new Revision('HEAD');
        $this->assertSame('HEAD', $revision->getAsString());
    }

    /**
     * @expectedException RestfulSubversion\Core\RevisionInvalidRevisionNumberCoreException
     */
    public function test_invalidNumbersThrowExceptionFloat()
    {
        $revision = new Revision(1.3234);
    }

    /**
     * @expectedException RestfulSubversion\Core\RevisionInvalidRevisionNumberCoreException
     */
    public function test_invalidNumbersThrowExceptionArbitraryString()
    {
        $revision = new Revision('Hello World');
    }

    /**
     * @expectedException RestfulSubversion\Core\RevisionInvalidRevisionNumberCoreException
     */
    public function test_invalidNumbersThrowExceptionNegative()
    {
        $revision = new Revision('-12345');
    }

    public function test_getRevertedAstring()
    {
        $revision = new Revision('12345');
        $this->assertSame('-12345', $revision->sGetRevertedAstring());
    }

    public function test_getAsString()
    {
        $revision = new Revision('12345');
        $this->assertSame('12345', $revision->getAsString());
    }

    public function test_toString()
    {
        $revision = new Revision('12345');
        $this->assertSame('12345', "$revision");
    }
}
