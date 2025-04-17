<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TransactionTypesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TransactionTypesTable Test Case
 */
class TransactionTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TransactionTypesTable
     */
    protected $TransactionTypes;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
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
        $config = $this->getTableLocator()->exists('TransactionTypes') ? [] : ['className' => TransactionTypesTable::class];
        $this->TransactionTypes = $this->getTableLocator()->get('TransactionTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->TransactionTypes);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\TransactionTypesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
