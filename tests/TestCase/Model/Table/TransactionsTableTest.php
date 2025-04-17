<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TransactionsTable;
use App\Test\TestHelperTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TransactionsTable Test Case
 */
class TransactionsTableTest extends TestCase
{
    use TestHelperTrait;

    /**
     * Test subject
     *
     * @var \App\Model\Table\TransactionsTable
     */
    protected $Transactions;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Transactions',
        'app.Users',
        'app.TransactionTypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Transactions') ? [] : ['className' => TransactionsTable::class];
        $this->Transactions = $this->getTableLocator()->get('Transactions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Transactions);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\TransactionsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->defaultTestAccessible($this->Transactions);

        $this->defaultTestValidEntity($this->Transactions, [
            'user_id' => 1,
            'amount' => 10,
            'time' => 3,
            'transaction_type_id' => 1,
        ]);

        $this->defaultTestInvalidEntity($this->Transactions, [
            'user_id' => 1,
            'amount' => null,
            'time' => 3,
        ]);
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\TransactionsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $validEntity = $this->Transactions->newEntity([
            'user_id' => 1,
            'amount' => 10,
            'time' => 3,
            'transaction_type_id' => 1,
        ]);
        $this->defaultTestForeignKeysValid($this->Transactions, $validEntity);


        $invalidEntity = $this->Transactions->newEntity([
            'user_id' => 100,
            'amount' => 10,
            'time' => 3,
            'transaction_type_id' => 100,
        ]);
        $this->defaultTestForeignKeyErrors($this->Transactions, $invalidEntity, [
            'user_id',
            'transaction_type_id'
        ]);
    }
}
