<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TransactionTypesFixture
 */
class TransactionTypesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'name' => 'deposit',
            ],
            [
                'id' => 2,
                'name' => 'withdrawal'
            ]
        ];
        parent::init();
    }
}
