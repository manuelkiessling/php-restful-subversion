<?php
/**
namespace RestfulSubversion\Core;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $content = new Content();
        $content->setRevision(new Revision('1234'));
        $content->setMimeType();
    }
} */