<?php

declare(strict_types=1);

namespace App\Test;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Exception;
use ReflectionProperty;

trait TestHelperTrait
{
     /**
     * Stores lists of fixture objects indexed by their class name.
     *
     * @var array<string, \Cake\Datasource\FixtureInterface>
     */
    private static $fixtureCache = [];

    protected function login($userId = 0)
    {
        $users = TableRegistry::getTableLocator()->get('Users');
        $user = $users->get($userId);
        $this->session(['Auth' => $user]);
    }

    public function getFixtureData(string $fixture, ?int $key = null): array
    {
        // Check for full defined class e.g. UserFixtures::class
        if (class_exists($fixture)) {
            $className = $fixture;
        } else {
            // Expected default behaviour e.g. getFixtureData('Users')
            $className = 'App\\Test\\Fixture\\' . $fixture . 'Fixture';
        }

        if (!class_exists($className)) {
            throw new Exception('Class ' . $className . ' not found.');
        }

        //Read the requested fixture from cache if exists.
        if (!isset(self::$fixtureCache[$className])) {
            //Because of the new fixture loading changes, need to use reflection to get the records property now.
            $strategy = $this->fixtureStrategy;
            $ref = new ReflectionProperty($strategy, 'fixtures');
            $ref->setAccessible(true);
            $fixtures = $ref->getValue($strategy);

            //Cache the requested fixture for future calls to reduce reflection usage.
            $fixture = self::$fixtureCache[$className] = $fixtures[$className];
        } else {
            $fixture = self::$fixtureCache[$className];
        }

        if (is_null($key)) {
            return $fixture->records;
        }

        return $fixture->records[$key];
    }

    /**
     * One line function to load a table with the method created by CakePHP bake
     *
     * @param string $name Table name e.g. Users, ClientRegions
     * @param class-string|null $class The FQN to the table, leave blank to default to App\Model\Table\$nameTable
     * @return \Cake\ORM\Table
     */
    public function loadTable(string $name, ?string $class = null): Table
    {
        $class = $class ?: 'App\\Model\\Table\\' . $name . 'Table';

        $config = $this->getTableLocator()->exists($name) ? [] : ['className' => $class];

        return $this->getTableLocator()->get($name, $config);
    }

    /**
     * Tests that an entity's accessible fields include all columns in the table schema, excluding the primary keys.
     *
     * @param \Cake\ORM\Table $table The table object used to create the entity to test and check the database columns.
     * @return void
     */
    public function defaultTestAccessible(Table $table): void
    {
        $tableName = Inflector::singularize($table->getAlias());

        $entity = $table->newEmptyEntity();
        $columns = $table->getSchema()->columns();
        $pk = $table->getSchema()->getPrimaryKey();

        foreach ($columns as $column) {
            $accessible = $entity->isAccessible($column);
            if (in_array($column, $pk)) {
                $this->assertFalse($accessible, "Field $column exists is included in the primary key for table $tableName, but is marked as accessible in the entity class.");
            } else {
                $this->assertTrue($accessible, "Field $column exists in the database schema for table $tableName, but is not marked as accessible in the entity class.");
            }
        }
    }

    /**
     * Test the creation of an entity with the provided data that you expect to be valid.
     *
     * @param \Cake\ORM\Table $table The table object used to create the entity to test.
     * @param array<string, mixed> $data An array of data used to instantiate the entity.
     * @param array<string, mixed> $options An array of options to pass to Table::newEntity.
     * @return \Cake\Datasource\EntityInterface The entity that was created and successfully validated.
     */
    public function defaultTestValidEntity(Table $table, array $data, array $options = []): EntityInterface
    {
        $tableName = Inflector::singularize($table->getAlias());

        $entity = $table->newEntity($data, $options);
        $this->assertFalse($entity->hasErrors(), "$tableName entity should have no errors but contains the following:\n" . json_encode($entity->getErrors(), JSON_PRETTY_PRINT));

        return $entity;
    }

    /**
     * Test the creation of an entity with the provided data that you expect to be invalid.
     *
     * @param \Cake\ORM\Table $table The table object used to create the entity to test.
     * @param array<string, mixed> $data An array of data used to instantiate the entity.
     * @param array<string, mixed> $options An array of options to pass to Table::newEntity.
     * @return \Cake\Datasource\EntityInterface The entity that was created and successfully proved invalid.
     */
    public function defaultTestInvalidEntity(Table $table, array $data, array $options = []): EntityInterface
    {
        $tableName = Inflector::singularize($table->getAlias());

        $entity = $table->newEntity($data, $options);
        $this->assertTrue($entity->hasErrors(), "$tableName entity should have at least one error.");

        return $entity;
    }

    /**
     * Test the creation of a blank entity (instantiated with []) that you expect to be invalid.
     *
     * @param \Cake\ORM\Table $table The table object used to create the entity to test.
     * @param string[] $requiredFields An array of fields that are expected to have a '_required' validation error.
     * @param string[] $unrequiredFields An array of fields that are not expected to have a '_required' validation error.
     * @param array<string, mixed> $options An array of options to pass to Table::newEntity.
     * @return \Cake\Datasource\EntityInterface The entity that was created and successfully proved invalid.
     */
    public function defaultTestBlankEntity(Table $table, array $requiredFields = [], array $unrequiredFields = [], array $options = []): EntityInterface
    {
        $entity = $this->defaultTestInvalidEntity($table, [], $options);

        foreach ($requiredFields as $field) {
            $this->assertArrayHasKey('_required', $entity->getError($field), "Failed asserting that field $field is required.");
        }
        foreach ($unrequiredFields as $field) {
            $this->assertArrayNotHasKey('_required', $entity->getError($field), "Failed asserting that field $field is not required.");
        }

        return $entity;
    }

    /**
     * Test an entity's fields have been marked as errored for being blank
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity which has errors to test.
     * @param string[] $emptyFields An array of fields that are expected to have a '_empty' validation error.
     * @param string[] $unemptyFields An array of fields that are not expected to have a '_empty' validation error.
     * @return void
     */
    public function defaultTestEmptyFields(EntityInterface $entity, array $emptyFields, array $unemptyFields = []): void
    {
        foreach ($emptyFields as $field) {
            $this->assertArrayHasKey('_empty', $entity->getError($field), "Failed asserting that field $field cannot be empty.");
        }
        foreach ($unemptyFields as $field) {
            $this->assertArrayNotHasKey('_empty', $entity->getError($field), "Failed asserting that field $field can be empty.");
        }
    }

    /**
     * Quickly tests the validator of a simple entity with only ID and name columns.
     *
     * @param \Cake\ORM\Table $table The table object used to create the entity to test.
     * @return void
     */
    public function defaultTestSimpleIdNameValidation(Table $table): void
    {
        //Test an entity that should be successfully validated.
        $this->defaultTestValidEntity($table, [
            'name' => 'New Entity',
        ]);

        //Test a blank entity.
        $this->defaultTestBlankEntity($table, ['name']);

        //Test an entity with empty fields.
        $empty = $this->defaultTestInvalidEntity($table, [
            'name' => '',
        ]);
        $this->defaultTestEmptyFields($empty, ['name']);
    }

    /**
     * Quickly tests an entity with invalid foreign keys
     * 
     * @param \Cake\ORM\Table $table the table to use to test foreign keys
     * @param \Cake\Datasource\EntityInterface $entity the entity with foreign key errors to test
     * @param string[] $foreignKeys an array of fields that are expected to error
     * 
     * @return void
     */
    public function defaultTestForeignKeyErrors(Table $table, EntityInterface $entity, array $foreignKeys): void
    {
        $result = $table->checkRules($entity);

        $this->assertFalse($result);
        $errors = $entity->getErrors();
        
        // Assert that the foreign keys passed in are in the error array
        foreach ($foreignKeys as $key) {
            $this->assertArrayHasKey($key, $errors);
        }

        // Assert that the errors are a non existing value
        foreach ($errors as $key => $error) {
            $this->assertArrayHasKey('_existsIn', $error);
            $this->assertEquals($error['_existsIn'], 'This value does not exist');
        }
    }

    /**
     * Quickly tests an entity with valid foreign keys
     * 
     * @param \Cake\ORM\Table $table the table to use to test foreign keys
     * @param \Cake\Datasource\EntityInterface $entity the entity with foreign key to test
     * 
     * @return void
     */
    public function defaultTestForeignKeysValid(Table $table, EntityInterface $entity): void
    {
        $result = $table->checkRules($entity);
        $this->assertTrue($result);
    }

    public function defaultTestGet(string $templateName, string $contentType = 'text/html'): void
    {
        $this->assertResponseSuccess();
        $this->assertResponseNotEmpty();
        $this->assertContentType($contentType);
        $this->assertTemplate($templateName);
    }
}
