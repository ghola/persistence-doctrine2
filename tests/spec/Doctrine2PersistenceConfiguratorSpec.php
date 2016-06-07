<?php

namespace spec\PSB\Persistence\Doctrine2;

use Doctrine\DBAL\Connection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PSB\Core\Util\Settings;
use PSB\Persistence\Doctrine2\Doctrine2KnownSettingsEnum;
use PSB\Persistence\Doctrine2\Doctrine2PersistenceConfigurator;

/**
 * @mixin Doctrine2PersistenceConfigurator
 */
class Doctrine2PersistenceConfiguratorSpec extends ObjectBehavior
{
    /**
     * @var Settings
     */
    protected $settingsMock;

    function let(Settings $settings)
    {
        $this->settingsMock = $settings;
        $this->beConstructedWith($settings);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PSB\Persistence\Doctrine2\Doctrine2PersistenceConfigurator');
    }

    function it_uses_doctrine_dbal_connection_parameters()
    {
        $this->settingsMock->set(
            Doctrine2KnownSettingsEnum::CONNECTION_PARAMETERS,
            ['some' => 'param']
        )->shouldBeCalled();

        $this->useConnectionParameters(['some' => 'param'])->shouldReturn($this);
    }

    function it_uses_doctrine_dbal_connection(Connection $dbalConnection)
    {
        $this->settingsMock->set(
            Doctrine2KnownSettingsEnum::CONNECTION,
            $dbalConnection
        )->shouldBeCalled();

        $this->useConnection($dbalConnection)->shouldReturn($this);
    }

    function it_uses_outbox_messages_table_name($tableName)
    {
        $this->settingsMock->set(
            Doctrine2KnownSettingsEnum::OUTBOX_MESSAGES_TABLE_NAME,
            $tableName
        )->shouldBeCalled();

        $this->useOutboxMessagesTableName($tableName)->shouldReturn($this);
    }

    function it_uses_outbox_endpoints_table_name($tableName)
    {
        $this->settingsMock->set(
            Doctrine2KnownSettingsEnum::OUTBOX_ENDPOINTS_TABLE_NAME,
            $tableName
        )->shouldBeCalled();

        $this->useOutboxEndpointsTableName($tableName)->shouldReturn($this);
    }
}
