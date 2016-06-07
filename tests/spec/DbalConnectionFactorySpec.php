<?php

namespace spec\PSB\Persistence\Doctrine2;

use Doctrine\DBAL\Connection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PSB\Persistence\Doctrine2\DbalConnectionFactory;
use PSB\Persistence\Doctrine2\Doctrine2KnownSettingsEnum;
use PSB\Core\Util\Settings;

/**
 * @mixin DbalConnectionFactory
 */
class DbalConnectionFactorySpec extends ObjectBehavior
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
        $this->shouldHaveType('PSB\Persistence\Doctrine2\DbalConnectionFactory');
    }

    function it_creates_by_returning_the_connection_from_settings_if_it_is_set(Connection $connection)
    {
        $this->settingsMock->tryGet(Doctrine2KnownSettingsEnum::CONNECTION)->willReturn($connection);

        $this->__invoke()->shouldReturn($connection);
    }

    function it_throws_if_no_connection_and_no_connection_parameters_are_found_in_settings()
    {
        $this->settingsMock->tryGet(Doctrine2KnownSettingsEnum::CONNECTION)->willReturn(null);
        $this->settingsMock->tryGet(Doctrine2KnownSettingsEnum::CONNECTION_PARAMETERS)->willReturn(null);

        $this->shouldThrow('PSB\Core\Exception\UnexpectedValueException')->during('__invoke');
    }

    function it_creates_a_new_connection_if_connection_parameters_found_in_settings()
    {
        $this->settingsMock->tryGet(Doctrine2KnownSettingsEnum::CONNECTION)->willReturn(null);
        $this->settingsMock->tryGet(Doctrine2KnownSettingsEnum::CONNECTION_PARAMETERS)->willReturn(
            [
                'dbname' => 'irrelevant',
                'user' => 'irrelevant',
                'password' => 'irrelevant',
                'host' => 'irrelevant',
                'driver' => 'pdo_mysql',
            ]
        );

        $this->__invoke()->shouldHaveType('Doctrine\DBAL\Connection');
    }
}
