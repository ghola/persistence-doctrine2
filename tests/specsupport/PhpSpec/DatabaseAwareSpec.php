<?php
namespace specsupport\PSB\Persistence\Doctrine2\PhpSpec;


use commonsupport\PSB\Persistence\Doctrine2\SchemaHelper;
use commonsupport\PSB\Persistence\Doctrine2\SchemaHelperInterface;
use Doctrine\DBAL\Connection;
use PhpSpec\ObjectBehavior;
use specsupport\PSB\Persistence\Doctrine2\DatabaseAwareSpecInterface;

abstract class DatabaseAwareSpec extends ObjectBehavior implements DatabaseAwareSpecInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var SchemaHelper
     */
    protected $schemaHelper;

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param \commonsupport\PSB\Persistence\Doctrine2\SchemaHelperInterface $schemaHelper
     */
    public function setSchemaHelper(SchemaHelperInterface $schemaHelper)
    {
        $this->schemaHelper = $schemaHelper;
    }
}
