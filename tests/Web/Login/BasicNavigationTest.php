<?php


namespace App\Tests\Web\Login;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicNavigationTest extends WebTestCase
{
    public function testHome(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello BaseController!');
        $this->assertSelectorTextContains('#login', 'Go to login');
    }

    public function testDashboardFail(): void
    {
        $client = static::createClient();
        $client->request('GET', '/dashboard');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDashboardSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Login');
        $client->submitForm('Sign in', [
            'email' => 'admin@email.test',
            'password' => 'password1admin',
        ]);
        $client->request('GET', '/dashboard');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1','This is your private zone');
    }
}