<?php

require_once __DIR__ . '/../NameParser.php';

use PHPUnit\Framework\TestCase;

class NameParserTest extends TestCase
{
    private NameParser $parser;

    protected function setUp(): void
    {
        $this->parser = new NameParser();
    }

    /* 
    * Tests a specific string input contaninig only 1 name,
    * to check if it has been correctly parsed.
    */
    public function testSingleName(): void
    {
        $result = $this->parser->parse('Mr John Smith');
        $this->assertCount(1, $result);
        
        $person = $result[0]->toArray();
        $this->assertEquals('Mr', $person['title']);
        $this->assertEquals('John', $person['first_name']);
        $this->assertNull($person['initial']);
        $this->assertEquals('Smith', $person['last_name']);
    }

    /* 
    * Tests a specific string input contaninig 2 names,
    * to check if they have been correctly parsed separately..
    */
    public function testCoupleNames(): void
    {
        $result = $this->parser->parse('Mr and Mrs Smith');
        $this->assertCount(2, $result);
        
        $this->assertEquals('Mr', $result[0]->title);
        $this->assertEquals('Smith', $result[0]->last_name);
        
        $this->assertEquals('Mrs', $result[1]->title);
        $this->assertEquals('Smith', $result[1]->last_name);
    }
}