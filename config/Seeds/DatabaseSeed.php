<?php
declare(strict_types=1);

use Migrations\BaseSeed;

class DatabaseSeed extends BaseSeed
{
    public function run(): void
    {
        // Generic things needed first
        $this->call('UsersSeed');
        $this->call('TransactionTypesSeed');
    }
}