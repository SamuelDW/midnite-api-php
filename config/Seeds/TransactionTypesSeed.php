<?php
declare(strict_types=1);

use Migrations\BaseSeed;

/**
 * TransactionTypes seed.
 */
class TransactionTypesSeed extends BaseSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/migrations/4/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'deposit',
            ],
            [
                'id' => 2,
                'name' => 'withdrawal'
            ],
        ];

        $table = $this->table('transaction_types');
        $table->insert($data)->save();
    }
}
