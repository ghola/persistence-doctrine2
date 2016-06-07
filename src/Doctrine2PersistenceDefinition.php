<?php
namespace PSB\Persistence\Doctrine2;


use PSB\Core\Feature\FeatureSettingsExtensions;
use PSB\Persistence\Doctrine2\Outbox\Doctrine2OutboxPersistenceFeature;
use PSB\Core\Persistence\PersistenceDefinition;
use PSB\Core\Persistence\StorageType;
use PSB\Core\Util\Settings;

class Doctrine2PersistenceDefinition extends PersistenceDefinition
{
    /**
     * @param Settings $settings
     *
     * @return Doctrine2PersistenceConfigurator
     */
    public function createConfigurator(Settings $settings)
    {
        return new Doctrine2PersistenceConfigurator($settings);
    }

    public function formalize()
    {
        $this->supports(
            StorageType::OUTBOX(),
            function (Settings $s) {
                FeatureSettingsExtensions::enableFeatureByDefault(
                    Doctrine2OutboxPersistenceFeature::class,
                    $s
                );
            }
        );
    }
}
