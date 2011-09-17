<?php

namespace RestfulSubversion\Core;

class RepoLogInterpreterTest extends \PHPUnit_Framework_TestCase
{
    public function test_createChangesetsFromXmlOneRevision()
    {
        $xml = <<<EOT
<?xml version="1.0"?>
<log>
<logentry
   revision="12345">
<author>Han Solo</author>
<date>2011-02-17T09:58:40.648317Z</date>
<paths>
<path
   kind=""
   action="A">/foo/bar.php</path>
<path
   kind=""
   action="M">/foo/foo.php</path>
<path
   kind=""
   copyfrom-path="/foo/sourcefile.php"
   copyfrom-rev="12344"
   action="A">/foo/targetfile.php</path>
<path
   kind=""
   action="D">/foo/other.php</path>
</paths>
<msg>DEV-5678: Hello World
- This is foo bar
</msg>
</logentry>
</log>
EOT;

        $expected = array();

        $expectedChangeset = new Changeset(new Revision('12345'));
        $expectedChangeset->setAuthor('Han Solo');
        $expectedChangeset->setDateTime('2011-02-17 10:58:40');
        $expectedChangeset->setMessage("DEV-5678: Hello World\n- This is foo bar\n");
        $expectedChangeset->addPathOperation('A', new RepoPath('/foo/bar.php'));
        $expectedChangeset->addPathOperation('M', new RepoPath('/foo/foo.php'));
        $expectedChangeset->addPathOperation('A', new RepoPath('/foo/targetfile.php'), new RepoPath('/foo/sourcefile.php'), new Revision('12344'));
        $expectedChangeset->addPathOperation('D', new RepoPath('/foo/other.php'));

        $expected[] = $expectedChangeset;

        $logInterpreter = new RepoLogInterpreter();
        $actual = $logInterpreter->createChangesetsFromVerboseXml($xml);

        $this->assertEquals($expected, $actual);
    }

    public function test_getChangesetFromXmlTwoRevisions()
    {
        $xml = <<<EOT
<?xml version="1.0"?>
<log>
<logentry
   revision="12345">
<author>Han Solo</author>
<date>2011-02-17T09:58:40.648317Z</date>
<paths>
<path
   kind=""
   action="A">/foo/bar.php</path>
<path
   kind=""
   action="M">/foo/foo.php</path>
<path
   kind=""
   copyfrom-path="/foo/sourcefile.php"
   copyfrom-rev="12344"
   action="A">/foo/targetfile.php</path>
<path
   kind=""
   action="D">/foo/other.php</path>
</paths>
<msg>DEV-5678: Hello World
- This is foo bar
</msg>
</logentry>
<logentry
   revision="12346">
<author>Luke Skywalker</author>
<date>2011-02-17T09:59:40.648317Z</date>
<paths>
<path
   kind=""
   copyfrom-path="/foo/sourcefile2.php"
   copyfrom-rev="999"
   action="A">/foo/targetfile2.php</path>
<path
   kind=""
   action="M">/foo/bar.php</path>
</paths>
<msg>DEV-5679: Goodbye World</msg>
</logentry>
</log>
EOT;

        $expected = array();

        $expectedChangeset = new Changeset(new Revision('12345'));
        $expectedChangeset->setAuthor('Han Solo');
        $expectedChangeset->setDateTime('2011-02-17 10:58:40');
        $expectedChangeset->setMessage("DEV-5678: Hello World\n- This is foo bar\n");
        $expectedChangeset->addPathOperation('A', new RepoPath('/foo/bar.php'));
        $expectedChangeset->addPathOperation('M', new RepoPath('/foo/foo.php'));
        $expectedChangeset->addPathOperation('A', new RepoPath('/foo/targetfile.php'), new RepoPath('/foo/sourcefile.php'), new Revision('12344'));
        $expectedChangeset->addPathOperation('D', new RepoPath('/foo/other.php'));

        $expected[] = $expectedChangeset;

        $expectedChangeset = new Changeset(new Revision('12346'));
        $expectedChangeset->setAuthor('Luke Skywalker');
        $expectedChangeset->setDateTime('2011-02-17 10:59:40');
        $expectedChangeset->setMessage("DEV-5679: Goodbye World");
        $expectedChangeset->addPathOperation('A', new RepoPath('/foo/targetfile2.php'), new RepoPath('/foo/sourcefile2.php'), new Revision('999'));
        $expectedChangeset->addPathOperation('M', new RepoPath('/foo/bar.php'));

        $expected[] = $expectedChangeset;

        $logInterpreter = new RepoLogInterpreter();
        $actual = $logInterpreter->createChangesetsFromVerboseXml($xml);

        $this->assertEquals($expected, $actual);
    }
}
