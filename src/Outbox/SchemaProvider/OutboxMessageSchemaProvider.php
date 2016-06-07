<?php
namespace PSB\Persistence\Doctrine2\Outbox\SchemaProvider;


use Doctrine\DBAL\Schema\Schema;
use PSB\Persistence\Doctrine2\SchemaProviderInterface;

class OutboxMessageSchemaProvider implements SchemaProviderInterface
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
            $table->addColumn('id', 'bigint', ['autoincrement' => true]);
            $table->addColumn('endpoint_id', 'integer');
            $table->addColumn('message_id', 'binary', ['length' => 16, 'fixed' => true]);
            $table->addColumn('is_dispatched', 'smallint');
            $table->addColumn('dispatched_at', 'datetime', ['notnull' => false]);
            $table->addColumn('transport_operations', 'text');
            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['message_id', 'endpoint_id']);
            $table->addIndex(['dispatched_at', 'is_dispatched']);
        }

        return $this->schemas[$tableName];
    }
}
