<?php
namespace Derasy\DerasyBundle\Tests;

use Derasy\DerasyBundle\DerasyGreeting;
use Derasy\DerasyBundle\DerasySalamProvider;
use PHPUnit\Framework\TestCase;

class DerasyTest extends TestCase
{
    public function testGetWords()
    {
        $greeting = new DerasyGreeting(new DerasySalamProvider());
        $salams = $greeting->getSalamList();
        $this->assertEquals(3, sizeof($salams));
    }
}