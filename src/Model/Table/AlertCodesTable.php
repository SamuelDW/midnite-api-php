<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AlertCodes Model
 *
 * @method \App\Model\Entity\AlertCode newEmptyEntity()
 * @method \App\Model\Entity\AlertCode newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\AlertCode> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AlertCode get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\AlertCode findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\AlertCode patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\AlertCode> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\AlertCode|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\AlertCode saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\AlertCode>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AlertCode>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\AlertCode>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AlertCode> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\AlertCode>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AlertCode>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\AlertCode>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AlertCode> deleteManyOrFail(iterable $entities, array $options = [])
 */
class AlertCodesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('alert_codes');
        $this->setDisplayField('reason');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('code')
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('reason')
            ->maxLength('reason', 255)
            ->requirePresence('reason', 'create')
            ->notEmptyString('reason');

        return $validator;
    }
}
