<?php

namespace spec\PSB\Persistence\Doctrine2\Outbox;

use Doctrine\DBAL\Exception\TableExistsException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Synchronizer\SingleDatabaseSynchronizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PSB\Persistence\Doctrine2\Outbox\OutboxTablesCreatorFeatureInstallTask;
use PSB\Persistence\Doctrine2\Outbox\SchemaProvider\OutboxEndpointSchemaProvider;
use PSB\Persistence\Doctrine2\Outbox\SchemaProvider\OutboxMessageSchemaProvider;

/**
 * @mixin OutboxTablesCreatorFeatureInstallTask
 */
class OutboxTablesCreatorFeatureInstallTaskSpec extends ObjectBehavior
{
    /**
     * @var SingleDatabaseSynchronizer
     */
    private $databaseSynchronizerMock;

    /**
     * @var OutboxEndpointSchemaProvider
     */
    private $endpointSchemaProviderMock;

    /**
     * @var OutboxMessageSchemaProvider
     */
    private $messagesSchemaProviderMock;

    private $endpointsTableName = 'endpoints_table';

    private $messagesTableName = 'messages_table';

    function let(
        SingleDatabaseSynchronizer $databaseSynchronizer,
        OutboxEndpointSchemaProvider $endpointSchemaProvider,
        OutboxMessageSchemaProvider $messagesSchemaProvider
    ) {
        $this->databaseSynchronizerMock = $databaseSynchronizer;
        $this->endpointSchemaProviderMock = $endpointSchemaProvider;
        $this->messagesSchemaProviderMock = $messagesSchemaProvider;
        $this->beConstructedWith(
            $databaseSynchronizer,
            $endpointSchemaProvider,
            $messagesSchemaProvider,
            $this->endpointsTableName,
            $this->messagesTableName
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PSB\Persistence\Doctrine2\Outbox\OutboxTablesCreatorFeatureInstallTask');
    }

    function it_creates_the_tables_for_all_outbox_schemas(Schema $messageSchema, Schema $endpointSchema)
    {
        $this->messagesSchemaProviderMock->getSchemaFor($this->messagesTableName)->willReturn($messageSchema);
        $this->endpointSchemaProviderMock->getSchemaFor($this->endpointsTableName)->willReturn($endpointSchema);

        $this->databaseSynchronizerMock->createSchema($messageSchema)->shouldBeCalled();
        $this->databaseSynchronizerMock->createSchema($endpointSchema)->shouldBeCalled();

        $this->install();
    }

    function it_does_not_throw_if_tables_already_exist(Schema $messageSchema, Schema $endpointSchema) {
        $this->messagesSchemaProviderMock->getSchemaFor($this->messagesTableName)->willReturn($messageSchema);
        $this->endpointSchemaProviderMock->getSchemaFor($this->endpointsTableName)->willReturn($endpointSchema);

        $this->databaseSynchronizerMock->createSchema($messageSchema)->willThrow(TableExistsException::class);
        $this->databaseSynchronizerMock->createSchema($endpointSchema)->willThrow(TableExistsException::class);

        $this->shouldNotThrow()->duringInstall();
    }
}
