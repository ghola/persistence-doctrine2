<?php

namespace spec\PSB\Persistence\Doctrine2\Outbox;

use Doctrine\DBAL\Connection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PSB\Core\Feature\FeatureStateEnum;
use PSB\Core\ObjectBuilder\BuilderInterface;
use PSB\Core\Outbox\OutboxFeature;
use PSB\Core\Outbox\OutboxStorageInterface;
use PSB\Core\Pipeline\PipelineModifications;
use PSB\Core\Util\Settings;
use PSB\Persistence\Doctrine2\DbalConnectionFactory;
use PSB\Persistence\Doctrine2\Doctrine2KnownSettingsEnum;
use PSB\Persistence\Doctrine2\Outbox\Doctrine2OutboxPersistenceFeature;
use PSB\Persistence\Doctrine2\Outbox\OutboxPersister;

/**
 * @mixin Doctrine2OutboxPersistenceFeature
 */
class Doctrine2OutboxPersistenceFeatureSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PSB\Persistence\Doctrine2\Outbox\Doctrine2OutboxPersistenceFeature');
    }

    function it_describes_as_depending_on_outbox_feature_and_by_registering_defaults(Settings $settings)
    {
        $this->describe();
        $this->configureDefaults($settings);

        $this->getDependencies()->shouldReturn([[OutboxFeature::class]]);
        $settings->setDefault(
            Doctrine2KnownSettingsEnum::OUTBOX_MESSAGES_TABLE_NAME,
            'psb_outbox_messages'
        )->shouldBeCalled();
        $settings->setDefault(Doctrine2KnownSettingsEnum::OUTBOX_ENDPOINTS_TABLE_NAME, 'psb_outbox_endpoints')
            ->shouldBeCalled();

    }

    function it_registers_services_in_the_container_during_setup(
        Settings $settings,
        BuilderInterface $builder,
        PipelineModifications $pipelineModifications
    ) {
        $settings->tryGet(OutboxFeature::class)->willReturn(FeatureStateEnum::ACTIVE);

        $builder->defineSingleton(
            Connection::class,
            new DbalConnectionFactory($settings->getWrappedObject())
        )->shouldBeCalled();

        $builder->defineSingleton(OutboxPersister::class, Argument::type('\Closure'))->shouldBeCalled();
        $builder->defineSingleton(OutboxStorageInterface::class, Argument::type('\Closure'))->shouldBeCalled();

        $this->setup($settings, $builder, $pipelineModifications);
    }
}
