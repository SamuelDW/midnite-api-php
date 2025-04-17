<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\View\JsonView;
use Exception;

/**
 * 
 * @property \App\Model\Table\UsersTable $Users
 */
class MidniteController extends AppController
{

    /**
     * @var \App\Model\Table\UsersTable
     */
    private $Users;

    /**
     * @var \App\Model\Table\TransactionTypesTables
     */
    private $TransactionTypes;

    /**
     * @var \App\Model\Table\TransactionsTable
     */
    private $Transactions;

    // public function viewClasses(): array
    // {
    //     return [JsonView::class];
    // }

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Users = $this->fetchTable('Users');
        $this->TransactionTypes = $this->fetchTable('TransactionTypes');
        $this->Transactions = $this->fetchTable('Transactions');
    }

    public function index()
    {
        $this->request->allowMethod(['post']);

        $data = $this->request->getData();
        
        $userId = $data['user_id'];
        $transactionType = $data['type'];

        try {
            $user = $this->Users->get($userId);
        } catch (Exception $e) {
            $response = json_encode([
                'error' => 'User not found',
                'code' => 404,
            ]);
            return $this->response->withStatus(404)->withStringBody($response);
        }

        try {
            $transactionMethod = $this->TransactionTypes->getTransactionTypeByName($transactionType);
        } catch (Exception $e) {
            $response = json_encode([
                'error' => 'Unknown transaction type',
                'code' => 404,
                'message' => 'Accepted Types are deposit or withdrawal'
            ]);
            return $this->response->withStatus(404)->withStringBody($response);
        }

        $transaction = $this->Transactions->newEmptyEntity();
        $entityData = [
            'user_id' => $user->id,
            'transaction_type_id' => $transactionMethod->id,
            'amount' => $data['amount'],
            'time' => $data['time'],
        ];

        $transaction = $this->Transactions->patchEntity($transaction, $entityData);

        dd($transaction);
    }
}