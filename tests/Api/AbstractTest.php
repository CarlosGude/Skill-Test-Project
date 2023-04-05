<?php


namespace App\Tests\Api;



class AbstractTest
{
    public static function setUp(): void
    {
        echo(exec('php bin/console doctrine:database:drop --force') . PHP_EOL);
        echo(exec('php bin/console doctrine:database:create') . PHP_EOL);
        echo(exec('php bin/console doctrine:migrations:migrate -n') . PHP_EOL);
        echo(exec('php bin/console doctrine:fixtures:load -n') . PHP_EOL);
    }
}