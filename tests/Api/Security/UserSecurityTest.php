<?php


namespace App\Tests\Api\Security;


use App\Tests\Api\AbstractTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserSecurityTest extends KernelTestCase
{
    protected function setUp(): void
    {
        AbstractTest::setUp();
    }

    public function testAnUserCanEditAnotherUser():void
    {
        $this->assertTrue(false);
    }

    public function testSuccessLogin():void
    {
        $this->assertTrue(false);
    }

    public function testFailureLogin():void
    {
        $this->assertTrue(false);
    }
}