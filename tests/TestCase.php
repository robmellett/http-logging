<?php

namespace RobMellett\HttpLogging\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Monolog\Handler\NullHandler;
use Orchestra\Testbench\TestCase as Orchestra;
use RobMellett\HttpLogging\HttpLoggingServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'RobMellett\\HttpLogging\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            HttpLoggingServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('logging.channels.http_logging', [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_http-logging_table.php.stub';
        $migration->up();
        */
    }
}
