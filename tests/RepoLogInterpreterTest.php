<?php

class MergeHelper_RepoLogInterpreterTest extends PHPUnit_Framework_TestCase {

	public function test_getAllRevisionInfosFromXml() {
		$sXml = <<<EOT
<?xml version="1.0"?>
<log>
<logentry
   revision="12345">
<author>Han.Solo</author>
<date>2011-02-17T09:58:40.648317Z</date>
<paths>
<path
   kind=""
   action="A">/branches/production/v22/src/a/b.php</path>
<path
   kind=""
   action="M">/branches/production/v22/src/a/a.php</path>
</paths>
<path
   kind=""
   copyfrom-path="/branches/staging/sourcefile.php"
   copyfrom-rev="5505"
   action="A">/branches/production/targetfile.php</path>
<path
   kind=""
   action="D">/branches/production/otherfile.php</path>
<msg>DEV-5678: Hello World
- This is foo bar
</msg>
</logentry>
</log>
EOT;

		$aExpected = array('sRevision' => '12345',
		                   'author' => 'Han.Solo',
		                   'datetime' => '2011-02-17 09:58:40',
		                   'message' => "DEV-5678: Hello World\n- This is foo bar\n",
		                   'paths' => array(
		                   	0 => array('kind' => '',
		                                'action' => 'A',
		                                'path' => '/branches/production/v22/src/a/b.php'
		                               ),
		                     1 => array('kind' => '',
					                       'action' => 'M',
					                       'path' => '/branches/production/v22/src/a/a.php'
					                      ),
		                     2 => array('kind' => '',
					                       'action' => 'A',
					                       'copyfrom-path' => '/branches/staging/sourcefile.php',
					                       'copyfrom-rev' => '5505',
					                       'path' => '/branches/production/targetfile.php'
					                      ),
		                     2 => array('kind' => '',
					                       'action' => 'D',
					                       'path' => '/branches/production/otherfile.php'
					                      )
		                   )
		                  );

		$oInterpreter = new MergeHelper_RepoLogInterpreter();
		$aActual = $oInterpreter->getAllRevisionInfosFromXml($sXml);
		
		$this->assertSame($aExpected, $aActual);
		
	}

}
