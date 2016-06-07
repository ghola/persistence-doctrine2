<?php
namespace specsupport\PSB\Persistence\Doctrine2;


use commonsupport\PSB\Persistence\Doctrine2\SchemaHelperInterface;
use Doctrine\DBAL\Connection;

interface DatabaseAwareSpecInterface
{
    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection);

    /**
     * @param SchemaHelperInterface $schemaHelper
     */
    public function setSchemaHelper(SchemaHelperInterface $schemaHelper);
}
