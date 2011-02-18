<?php

class MergeHelper_RepoLogInterpreterTest extends PHPUnit_Framework_TestCase {

	public function test_getChangesetFromXml() {
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

		$oExpected = new MergeHelper_Changeset(new MergeHelper_Revision('12345'));
		$oExpected->setAuthor('Han Solo');
		$oExpected->setDateTime('2011-02-17 10:58:40');
		$oExpected->setMessage("DEV-5678: Hello World\n- This is foo bar\n");
		$oExpected->addPathOperation('A', new MergeHelper_RepoPath('/foo/bar.php'));
		$oExpected->addPathOperation('M', new MergeHelper_RepoPath('/foo/foo.php'));
		$oExpected->addPathOperation('A', new MergeHelper_RepoPath('/foo/targetfile.php'), new MergeHelper_RepoPath('/foo/sourcefile.php'), new MergeHelper_Revision('12344'));
		$oExpected->addPathOperation('D', new MergeHelper_RepoPath('/foo/other.php'));
		
		$oInterpreter = new MergeHelper_RepoLogInterpreter();
		$oActual = $oInterpreter->oGetChangesetFromXml($sXml);
				
		$this->assertEquals($oExpected, $oActual);
		
	}

}
