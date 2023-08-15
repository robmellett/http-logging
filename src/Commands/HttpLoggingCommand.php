<?php

namespace RobMellett\HttpLogging\Commands;

use Illuminate\Console\Command;

class HttpLoggingCommand extends Command
{
    public $signature = 'http-logging';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
