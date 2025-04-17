<?php

declare(strict_types=1);

namespace App\Utility;

use \App\Model\Entity\Transaction;
use \App\Model\Entity\TransactionType;

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
    public function isDepositingTooMuchTooQuickly(array $transactions): void
    {

    }
}