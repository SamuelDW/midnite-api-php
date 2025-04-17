<?php

declare(strict_types=1);

namespace App\Utility;

use \App\Model\Entity\Transaction;
use \App\Model\Entity\TransactionType;
use App\Model\Table\TransactionTypesTable;

class AlertCodes
{
    /**
     * 
     * @param \App\Model\Entity\Transaction $transaction
     * @param \App\Model\Entity\TransactionType $type
     * @return bool
     */
    public static function isOverWithdrawalLimit(Transaction $transaction, TransactionType $type): bool
    {
        if ($type->name !== 'withdrawal') {
            return false;
        }

        if ($transaction->amount < 100) {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param \App\Model\Entity\Transaction[] $transactions
     * @return bool
     */
    public static function hasWithdrawnThreeTimesInARow(array $transactions, int $withdrawalId): bool
    {
        if (count($transactions) < 3) {
            return false;
        }

        // Check only the first 3 transactions
        for ($i = 0; $i < 3; $i++) {
            if ($transactions[$i]->transaction_type_id !== $withdrawalId) {
                return false;
            }
        }

        return true;
    }

    /**
     * 
     * @param \App\Model\Entity\Transaction[] $transactions
     * @return bool
     */
    public static function hasDepositedGreaterAmountsConsecutively(array $transactions): bool
    {
        if (count($transactions) < 3) {
            return false;
        }

        return ($transactions[0]->amount > $transactions[1]->amount) && ($transactions[1]->amount > $transactions[2]->amount);    
    }

    /**
     * 
     * @param \App\Model\Entity\Transaction[] $transactions
     * @return bool
     */
    public static function isDepositingTooMuchTooQuickly(array $transactions): bool
    {
        if (count($transactions) === 0) {
            return false;
        }

        // If the first deposit is too much, don't waste time looping
        if ($transactions[0]->amount > 200 && $transactions[0]->transaction_type_id === TransactionTypesTable::DEPOSIT_ID) {
            return true;
        }

        $time = 0;
        $depositAmount = 0;

        foreach ($transactions as $transaction) {
            $time += $transaction->time;
            $depositAmount += $transaction->transaction_type_id === TransactionTypesTable::DEPOSIT_ID
                ? $transaction->amount
                : 0;

            // as soon as time is 30 or more and amount is over 200 return
            if ($time >= 30 && $depositAmount > 200) {
                return true;
            } elseif ($time >= 30 && $depositAmount < 200) {
                return false;
            }
        }

        return false;
    }
}