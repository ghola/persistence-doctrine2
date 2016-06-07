<?php
namespace commonsupport\PSB\Persistence\Doctrine2;


use Doctrine\DBAL\Schema\Schema;
use PSB\Persistence\Doctrine2\SchemaProviderInterface;

class TableConfig
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var SchemaProviderInterface
     */
    private $schemaProvider;

    /**
     * @param string                  $tableName
     * @param SchemaProviderInterface $schemaProvider
     */
    public function __construct($tableName, SchemaProviderInterface $schemaProvider)
    {
        $this->tableName = $tableName;
        $this->schemaProvider = $schemaProvider;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return SchemaProviderInterface
     */
    public function getSchemaProvider()
    {
        return $this->schemaProvider;
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schemaProvider->getSchemaFor($this->tableName);
    }
}
