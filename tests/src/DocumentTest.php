<?php

namespace RoNoLo\Flydb;

class DocumentTest extends TestBase
{
    public function testGettingId()
    {
        $doc = new Document([
            'mike' => 'snow',
        ]);

        $doc->setId('albums');
        $this->assertEquals('albums', $doc->getId());
    }

    public function testGettingInitialId()
    {
        $doc = new Document([
            'mike' => 'snow',
        ]);

        $doc->setId('albums');
        $this->assertEquals('albums', $doc->getInitialId());

        $doc->setId('singles');
        $this->assertEquals('albums', $doc->getInitialId());
    }
}
