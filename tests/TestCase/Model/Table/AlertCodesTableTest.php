<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AlertCodesTable;
use App\Test\TestHelperTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AlertCodesTable Test Case
 */
class AlertCodesTableTest extends TestCase
{
    use TestHelperTrait;

    /**
     * Test subject
     *
     * @var \App\Model\Table\AlertCodesTable
     */
    protected $AlertCodes;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.AlertCodes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('AlertCodes') ? [] : ['className' => AlertCodesTable::class];
        $this->AlertCodes = $this->getTableLocator()->get('AlertCodes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->AlertCodes);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\AlertCodesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->defaultTestAccessible($this->AlertCodes);

        $this->defaultTestValidEntity($this->AlertCodes, [
            'code' => 1999,
            'reason' => 'Testing'
        ]);

        $this->defaultTestInvalidEntity($this->AlertCodes, [
            'code' => null,
            'reason' => null
        ]);
    }
}
