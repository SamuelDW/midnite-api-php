<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\TransactionTypesTable;
use App\Utility\AlertCodes;
use Cake\View\JsonView;
use Exception;
use Cake\Http\Exception\BadRequestException;

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

    public function viewClasses(): array
    {
        return [JsonView::class];
    }

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

        // Check all fields are present, otherwise throw an error
        if (empty($data['user_id']) || empty($data['type']) || empty($data['amount']) || empty($data['time'])) {
            return $this->returnErrorResponse(403, 'Invalid Request');
        }

        $userId = $data['user_id'];
        $transactionType = $data['type'];

        // Locate the user, otherwise throw not found
        try {
            $user = $this->Users->get($userId);
        } catch (Exception $e) {
            return $this->returnErrorResponse(404, 'User not found');
        }

        // Determine if the transaction type is an allowed method
        try {
            $transactionMethod = $this->TransactionTypes->getTransactionTypeByName($transactionType);
        } catch (Exception $e) {
            return $this->returnErrorResponse(404, 'Transaction type not recognised', [
                'accepted_methods' => ['deposit', 'withdrawal']
            ]);
        }

        // If the action is withdrawal and the amount will take us below 0, prevent the transaction running
        if (($transactionMethod === 'withdrawal' && $user->total < 0) || ($transactionMethod === 'withdrawal' && $user->total - $data['amount'] < 0)) {
            return $this->returnErrorResponse(403, 'Insufficient Funds');
        }

        // Create the transaction and save it
        $transaction = $this->Transactions->newEmptyEntity();
        $entityData = [
            'user_id' => $user->id,
            'transaction_type_id' => $transactionMethod->id,
            'amount' => $data['amount'],
            'time' => $data['time'],
        ];

        $transaction = $this->Transactions->patchEntity($transaction, $entityData);

        if (!$this->Transactions->save($transaction)) {
            $response = json_encode([
                'error' => 500,
                'message' => 'Could not save transaction',
                'errors' => $transaction->getErrors(),
            ]);

            return $this->response->withStatus(500)->withStringBody($response);
        }

        // as we know the user has to have sufficient funds to make a withdrawal we can confifently add or subtract the amount
        if ($transactionMethod->name === 'deposit') {
            $user->total += $transaction->amount;
        } else {
            $user->total -= $transaction->amount;
        }

        $this->Users->save($user);

        // Grab three most recent transactions, which includes the one just saved. Only need to check the most recent three, don't need to grab the entire list.
        $transactionsByUser = $this->Transactions->find()->where([
            'user_id' => $user->id,
        ])->orderByDesc('Transactions.id')->limit(3)->all()->toArray();

        $depositsByUser = $this->Transactions->find()->where([
            'transaction_type_id' => TransactionTypesTable::DEPOSIT_ID,
            'user_id' => $user->id
        ])->orderByDesc('Transactions.id')->limit(3)->all()->toArray();

        $allDepositsByUser = $this->Transactions->find()->where([
            'user_id' => $user->id
        ])->orderByDesc('Transactions.id')->all()->toArray();

        // Get booleans for all the codes first, and then check all 

        if (
            AlertCodes::hasDepositedGreaterAmountsConsecutively($depositsByUser) &&
            $transaction->transaction_type_id === TransactionTypesTable::DEPOSIT_ID
        ) {
            $alertCodes[] = 300;
        }

        if (AlertCodes::isOverWithdrawalLimit($transaction, $transactionMethod)) {
            $alertCodes[] = 1100;
        }

        if (AlertCodes::hasWithdrawnThreeTimesInARow($transactionsByUser, TransactionTypesTable::WITHDRAWAL_ID)) {
            $alertCodes[] = 30;
        }

        if (AlertCodes::isDepositingTooMuchTooQuickly($allDepositsByUser) && $transaction->transaction_type_id === TransactionTypesTable::DEPOSIT_ID) {
            $alertCodes[] = 123;
        }

        $response = json_encode([
            'user_id' => $user->id,
            'alert' => !empty($alertCodes),
            'alert_codes' => empty($alertCodes) ? [] : $alertCodes
        ]);

        return $this->response->withStringBody($response)->withStatus(200)->withType('application/json');
    }

    private function returnErrorResponse(int $code, string $message, array $extra = [])
    {
        return $this->response->withStatus($code)->withStringBody(json_encode(array_merge([
            'error' => $message,
            'code' => $code,
        ], $extra)));
    }
}