<?php

class RestfulSubversion_Helper_AutoloaderTest extends PHPUnit_Framework_TestCase {

    public function test_existingFile() {
        $this->assertSame('Core/RepoCache.php', RestfulSubversion_Helper_Autoloader::load('RestfulSubversion_Core_RepoCache'));
    }

    public function test_nonExistantFile() {
        $this->assertFalse(RestfulSubversion_Helper_Autoloader::load('dewdew.php'));
    }

}
