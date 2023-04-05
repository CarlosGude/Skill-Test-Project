<?php


namespace App\Tests\Api;



class AbstractTest
{
    public static function setUp(): void
    {
        echo(exec('php bin/console cache:clear --env=test') . PHP_EOL);
        echo(exec('php bin/console doctrine:database:drop --force --env=test') . PHP_EOL);
        echo(exec('php bin/console doctrine:database:create --env=test') . PHP_EOL);
        echo(exec('php bin/console doctrine:migrations:migrate -n --env=test') . PHP_EOL);
        echo(exec('php bin/console doctrine:fixtures:load -n --env=test') . PHP_EOL);
    }

    public static function getBaseUrl(): string
    {
        return $_ENV['TEST_URL'];
    }
}