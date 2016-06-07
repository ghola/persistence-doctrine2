<?php
namespace PSB\Persistence\Doctrine2\Outbox;


use Doctrine\DBAL\Exception\TableExistsException;
use Doctrine\DBAL\Schema\Synchronizer\SingleDatabaseSynchronizer;
use PSB\Core\Feature\FeatureInstallTaskInterface;
use PSB\Persistence\Doctrine2\Outbox\SchemaProvider\OutboxEndpointSchemaProvider;
use PSB\Persistence\Doctrine2\Outbox\SchemaProvider\OutboxMessageSchemaProvider;

class OutboxTablesCreatorFeatureInstallTask implements FeatureInstallTaskInterface
{
    /**
     * @var SingleDatabaseSynchronizer
     */
    private $databaseSynchronizer;

    /**
     * @var OutboxEndpointSchemaProvider
     */
    private $endpointSchemaProvider;

    /**
     * @var OutboxMessageSchemaProvider
     */
    private $messagesSchemaProvider;

    /**
     * @var string
     */
    private $endpointsTableName;

    /**
     * @var string
     */
    private $messagesTableName;

    /**
     * @param SingleDatabaseSynchronizer   $databaseSynchronizer
     * @param OutboxEndpointSchemaProvider $endpointSchemaProvider
     * @param OutboxMessageSchemaProvider  $messagesSchemaProvider
     * @param string                       $endpointsTableName
     * @param string                       $messagesTableName
     */
    public function __construct(
        SingleDatabaseSynchronizer $databaseSynchronizer,
        OutboxEndpointSchemaProvider $endpointSchemaProvider,
        OutboxMessageSchemaProvider $messagesSchemaProvider,
        $endpointsTableName,
        $messagesTableName
    ) {
        $this->databaseSynchronizer = $databaseSynchronizer;
        $this->endpointSchemaProvider = $endpointSchemaProvider;
        $this->messagesSchemaProvider = $messagesSchemaProvider;
        $this->endpointsTableName = $endpointsTableName;
        $this->messagesTableName = $messagesTableName;
    }

    public function install()
    {
        $messageSchema = $this->messagesSchemaProvider->getSchemaFor($this->messagesTableName);
        $endpointSchema = $this->endpointSchemaProvider->getSchemaFor($this->endpointsTableName);

        // is there a better way to atomically create in order to not compete with other endpoints?
        try {
            $this->databaseSynchronizer->createSchema($messageSchema);
        } catch (TableExistsException $e) {
        }

        try {
            $this->databaseSynchronizer->createSchema($endpointSchema);
        } catch (TableExistsException $e) {
        }
    }
}
