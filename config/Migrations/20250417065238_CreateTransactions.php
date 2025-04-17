<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateTransactions extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('transactions');
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
            'signed' => false,
        ]);
        $table->addForeignKey('user_id', 'users');
        $table->addColumn('transaction_type_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
            'signed' => false,
        ]);
        $table->addForeignKey('transaction_type_id', 'transaction_types');
        $table->addColumn('amount', 'float', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('time', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->create();
    }
}
