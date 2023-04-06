<?php

namespace App\Tests\Web\Login;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{

    public function testLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Login');
        $client->submitForm('Sign in', [
            'email' => 'carlos@gmail.com',
            'password' => 'carlos@gmail.com',
        ]);

        $this->assertSelectorTextContains('h1', 'Hello BaseController!');
        $this->assertSelectorTextContains('h2', 'Carlos Gude');
    }

    public function testFailLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Login');
        $client->submitForm('Sign in', [
            'email' => 'fail@gmail.com',
            'password' => 'fail',
        ]);

        $this->assertSelectorTextContains('h1', 'Login');
        $this->assertSelectorTextContains('.alert', 'Invalid credentials.');
    }
}
