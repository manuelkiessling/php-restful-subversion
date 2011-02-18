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
<path
   kind=""
   copyfrom-path="/branches/staging/sourcefile.php"
   copyfrom-rev="5505"
   action="A">/branches/production/targetfile.php</path>
<path
   kind=""
   action="D">/branches/production/otherfile.php</path>
</paths>
<msg>DEV-5678: Hello World
- This is foo bar
</msg>
</logentry>
</log>
EOT;

		$aExpected = array('sRevision' => '12345',
		                   'sAuthor' => 'Han.Solo',
		                   'sDateTime' => '2011-02-17 10:58:40',
		                   'sMessage' => "DEV-5678: Hello World\n- This is foo bar\n",
		                   'aPathOperations' => array(
		                   	0 => array('sKind' => '',
		                                'sAction' => 'A',
		                                'sPath' => '/branches/production/v22/src/a/b.php'
		                               ),
		                     1 => array('sKind' => '',
					                       'sAction' => 'M',
					                       'sPath' => '/branches/production/v22/src/a/a.php'
					                      ),
		                     2 => array('sKind' => '',
					                       'sAction' => 'A',
					                       'sCopyfromPath' => '/branches/staging/sourcefile.php',
					                       'sCopyfromRev' => '5505',
					                       'sPath' => '/branches/production/targetfile.php'
					                      ),
		                     3 => array('sKind' => '',
					                       'sAction' => 'D',
					                       'sPath' => '/branches/production/otherfile.php'
					                      )
		                   )
		                  );

		$oInterpreter = new MergeHelper_RepoLogInterpreter();
		$aActual = $oInterpreter->aGetAllRevisionInfosFromXml($sXml);
				
		$this->assertSame($aExpected, $aActual);
		
	}

}
