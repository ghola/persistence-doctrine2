<?php
namespace PSB\Persistence\Doctrine2;


use Doctrine\DBAL\Schema\Schema;

interface SchemaProviderInterface
{
    /**
     * @param string $tableName
     *
     * @return Schema
     */
    public function getSchemaFor($tableName);
}
