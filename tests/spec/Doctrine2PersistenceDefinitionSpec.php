<?php

namespace spec\PSB\Persistence\Doctrine2;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PSB\Persistence\Doctrine2\Doctrine2PersistenceConfigurator;
use PSB\Persistence\Doctrine2\Doctrine2PersistenceDefinition;
use PSB\Core\Persistence\StorageType;
use PSB\Core\Util\Settings;

/**
 * @mixin Doctrine2PersistenceDefinition
 */
class Doctrine2PersistenceDefinitionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PSB\Persistence\Doctrine2\Doctrine2PersistenceDefinition');
    }

    function it_creates_a_persistence_configurator(Settings $settings)
    {
        $this->createConfigurator($settings)->shouldBeLike(
            new Doctrine2PersistenceConfigurator($settings->getWrappedObject())
        );
    }

    function it_formalizes_by_declaring_support_for_outbox()
    {
        $this->formalize();

        $this->hasSupportFor(StorageType::OUTBOX())->shouldReturn(true);
    }
}
