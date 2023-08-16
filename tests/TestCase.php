<?php

namespace RobMellett\HttpLogging\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use RobMellett\HttpLogging\HttpLoggingServiceProvider;
use RobMellett\HttpLogging\Support\SecureJsonFormatter;

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

        // Write to log file
        config()->set('logging.channels.http_logs', [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',

            'formatter' => SecureJsonFormatter::class,
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_http-logging_table.php.stub';
        $migration->up();
        */
    }
}
