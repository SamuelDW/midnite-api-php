<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class MidniteControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected array $fixtures = [
        'app.Users',
        'app.TransactionTypes',
        'app.Transactions',
    ];

    /**
     * @uses \App\Controller\MidniteController::index()
     * @return void
     */
    public function testGetIndex(): void
    {
        $this->get('/event');

        $this->assertResponseError();
    }

    public function testPostIndex(): void
    {
        $this->post('/event');

        $this->assertResponseCode(403, 'An empty response should fail');
    }


    public function testPostIndexWithIncorrectData(): void
    {
        $data = [
            'user_id' => 3, // user id does not exist
            'type' => 'deposit',
            'time' => 20,
            'amount' => 20.00
        ];

        $this->post('/event', $data);

        $this->assertResponseCode(404, 'User should not be found');
    }

    public function testPostIndexWithIncorrectType(): void
    {
        $data = [
            'user_id' => 1, // user id does not exist
            'type' => 'hacking',
            'time' => 20,
            'amount' => "20.00"
        ];

        $this->post('/event', $data);

        $this->assertResponseCode(404, 'Transaction type should not be found');
    }

    public function testPostIndexWithCorrectData(): void
    {
        $this->disableErrorHandlerMiddleware();
        $data = [
            'user_id' => 1, // user id does not exist
            'type' => 'deposit',
            'time' => 20,
            'amount' => "20.00"
        ];

        $this->post('/event', $data);
        // dd($this->_response->getBody());

        $this->assertResponseCode(200, 'User should be found');

        $expected = [
            'user_id' => 1,
            'alert' => false,
            'alert_codes' => [],
        ];
        $expected = json_encode($expected);
        $this->assertEquals($expected, (string) $this->_response->getBody());
    }

    public function testPostIndexWithBigDeposit(): void
    {
        $this->disableErrorHandlerMiddleware();
        $data = [
            'user_id' => 1, // user id does not exist
            'type' => 'deposit',
            'time' => 20,
            'amount' => "2000.00"
        ];

        $this->post('/event', $data);
        // dd($this->_response->getBody());

        $this->assertResponseCode(200, 'User should be found');

        $expected = [
            'user_id' => 1,
            'alert' => true,
            'alert_codes' => [123],
        ];
        $expected = json_encode($expected);
        $this->assertEquals($expected, (string) $this->_response->getBody());
    }

    public function testPostIndexWithBigWithdrawal(): void
    {
        $this->disableErrorHandlerMiddleware();
        $data = [
            'user_id' => 1, // user id does not exist
            'type' => 'withdrawal',
            'time' => 20,
            'amount' => "2000.00"
        ];

        $this->post('/event', $data);
        // dd($this->_response->getBody());

        $this->assertResponseCode(200, 'User should be found');

        $expected = [
            'user_id' => 1,
            'alert' => true,
            'alert_codes' => [1100],
        ];
        $expected = json_encode($expected);
        $this->assertEquals($expected, (string) $this->_response->getBody());
    }

}