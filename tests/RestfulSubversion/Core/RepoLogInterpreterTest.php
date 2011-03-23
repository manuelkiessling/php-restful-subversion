<?php

class RestfulSubversion_Core_RepoLogInterpreterTest extends PHPUnit_Framework_TestCase {

	public function test_createChangesetsFromXmlOneRevision() {
		$sXml = <<<EOT
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

		$aoExpected = array();

		$oExpected = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
		$oExpected->setAuthor('Han Solo');
		$oExpected->setDateTime('2011-02-17 10:58:40');
		$oExpected->setMessage("DEV-5678: Hello World\n- This is foo bar\n");
		$oExpected->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));
		$oExpected->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/foo.php'));
		$oExpected->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/foo/targetfile.php'), new RestfulSubversion_Core_RepoPath('/foo/sourcefile.php'), new RestfulSubversion_Core_Revision('12344'));
		$oExpected->addPathOperation('D', new RestfulSubversion_Core_RepoPath('/foo/other.php'));

		$aoExpected[] = $oExpected;

		$oInterpreter = new RestfulSubversion_Core_RepoLogInterpreter();
		$aoActual = $oInterpreter->aoCreateChangesetsFromVerboseXml($sXml);

		$this->assertEquals($aoExpected, $aoActual);
		
	}

	public function test_getChangesetFromXmlTwoRevisions() {
		$sXml = <<<EOT
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

		$aoExpected = array();

		$oExpected = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
		$oExpected->setAuthor('Han Solo');
		$oExpected->setDateTime('2011-02-17 10:58:40');
		$oExpected->setMessage("DEV-5678: Hello World\n- This is foo bar\n");
		$oExpected->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));
		$oExpected->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/foo.php'));
		$oExpected->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/foo/targetfile.php'), new RestfulSubversion_Core_RepoPath('/foo/sourcefile.php'), new RestfulSubversion_Core_Revision('12344'));
		$oExpected->addPathOperation('D', new RestfulSubversion_Core_RepoPath('/foo/other.php'));

		$aoExpected[] = $oExpected;

		$oExpected = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12346'));
		$oExpected->setAuthor('Luke Skywalker');
		$oExpected->setDateTime('2011-02-17 10:59:40');
		$oExpected->setMessage("DEV-5679: Goodbye World");
		$oExpected->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/foo/targetfile2.php'), new RestfulSubversion_Core_RepoPath('/foo/sourcefile2.php'), new RestfulSubversion_Core_Revision('999'));
		$oExpected->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));

		$aoExpected[] = $oExpected;

		$oInterpreter = new RestfulSubversion_Core_RepoLogInterpreter();
		$aoActual = $oInterpreter->aoCreateChangesetsFromVerboseXml($sXml);

		$this->assertEquals($aoExpected, $aoActual);

	}

}
