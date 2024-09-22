<?php

namespace tyasa81\RequestWrapper\Commands;

use Illuminate\Console\Command;

class RequestWrapperCommand extends Command
{
    public $signature = 'requestwrapper';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
