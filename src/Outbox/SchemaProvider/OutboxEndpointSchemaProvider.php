<?php
namespace PSB\Persistence\Doctrine2\Outbox\SchemaProvider;


use Doctrine\DBAL\Schema\Schema;
use PSB\Persistence\Doctrine2\SchemaProviderInterface;

class OutboxEndpointSchemaProvider implements SchemaProviderInterface
{
    /**
     * @var Schema[]
     */
    private $schemas = [];

    /**
     * @param string $tableName
     *
     * @return Schema
     */
    public function getSchemaFor($tableName)
    {
        if (!isset($this->schemas[$tableName])) {
            $this->schemas[$tableName] = new Schema();
            $table = $this->schemas[$tableName]->createTable($tableName);
            $table->addColumn('id', 'integer', ['autoincrement' => true]);
            $table->addColumn('lookup_hash', 'binary', ['length' => 16, 'fixed' => true]);
            $table->addColumn('name', 'text');
            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['lookup_hash']);
        }

        return $this->schemas[$tableName];
    }
}
