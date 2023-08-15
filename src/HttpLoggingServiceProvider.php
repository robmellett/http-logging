<?php

namespace RobMellett\HttpLogging;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use RobMellett\HttpLogging\Commands\HttpLoggingCommand;

class HttpLoggingServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('http-logging')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_http-logging_table')
            ->hasCommand(HttpLoggingCommand::class);
    }
}
