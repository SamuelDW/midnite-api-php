<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AlertCodesFixture
 */
class AlertCodesFixture extends TestFixture
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
                'code' => 1,
                'reason' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
