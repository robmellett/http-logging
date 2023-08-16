<?php

namespace RobMellett\HttpLogging\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Orchestra\Testbench\TestCase as Orchestra;
use RobMellett\HttpLogging\HttpLoggingServiceProvider;
use RobMellett\HttpLogging\Support\SecureMessageFormatter;

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
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',

            'formatter' => \Monolog\Formatter\JsonFormatter::class,
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_http-logging_table.php.stub';
        $migration->up();
        */
    }
}
