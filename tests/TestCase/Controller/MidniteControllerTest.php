<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class MidniteControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * @uses \App\Controller\MidniteController::index()
     * @return void
     */
    public function testGetIndex(): void
    {
        $this->get('/event');

        $this->assertResponseError();
    }
}