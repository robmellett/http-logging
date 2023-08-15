<?php

namespace RobMellett\HttpLogging;

use RobMellett\HttpLogging\Commands\HttpLoggingCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasConfigFile();
    }
}
