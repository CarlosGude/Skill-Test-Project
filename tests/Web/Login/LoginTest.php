<?php

namespace App\Tests\Web\Login;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    protected function setUp(): void
    {
        echo(exec('php bin/console cache:clear ') . PHP_EOL);
        echo(exec('php bin/console doctrine:database:drop --force ') . PHP_EOL);
        echo(exec('php bin/console doctrine:database:create ') . PHP_EOL);
        echo(exec('php bin/console doctrine:migrations:migrate -n ') . PHP_EOL);
        echo(exec('php bin/console doctrine:fixtures:load -n ') . PHP_EOL);
    }

    public function testLogin(): void
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

        $this->assertSelectorTextContains('h1', 'Welcome to ip-global blog');
    }

    public function testUnActiveUserLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Login');
        $client->submitForm('Sign in', [
            'email' => 'noActiveUser@email.test',
            'password' => 'noActiveUser1',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeletedUserLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Login');
        $client->submitForm('Sign in', [
            'email' => 'deletedUser@email.test',
            'password' => 'deletedUser1',
        ]);

        $this->assertResponseStatusCodeSame(403);
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
