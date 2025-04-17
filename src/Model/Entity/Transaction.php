<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Transaction Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $transaction_type_id
 * @property float $amount
 * @property int $time
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\TransactionType $transaction_type
 */
class Transaction extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'user_id' => true,
        'transaction_type_id' => true,
        'amount' => true,
        'time' => true,
        'user' => true,
        'transaction_type' => true,
    ];
}
