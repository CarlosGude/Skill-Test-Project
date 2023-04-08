<?php

namespace App\Tests\Web\Navigation;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardNavigationTest extends WebTestCase
{
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
        $client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(200);
    }
}
