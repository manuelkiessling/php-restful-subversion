<?php

namespace RestfulSubversion\Core;

class RepoInfoInterpreterTest extends \PHPUnit_Framework_TestCase
{
    public function test_getKindFromXml()
    {
        $xml = <<<EOT
<?xml version="1.0"?>
<info>
<entry
   kind="file"
   path="README"
   revision="2">
<url>file:///var/tmp/svn/README</url>
<repository>
<root>file:///var/tmp/svn</root>
<uuid>cbcc7160-a42f-45c2-b544-9acb97a4cf54</uuid>
</repository>
<commit
   revision="1">
<author>manuel</author>
<date>2011-09-23T06:31:30.710832Z</date>
</commit>
</entry>
</info>
EOT;

        $expected = "file";

        $infoInterpreter = new RepoInfoInterpreter();
        $actual = $infoInterpreter->getKindFromXml($xml);

        $this->assertEquals($expected, $actual);
    }
}
