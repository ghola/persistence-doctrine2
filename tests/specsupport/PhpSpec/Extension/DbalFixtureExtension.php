<?php
namespace specsupport\PSB\Persistence\Doctrine2\PhpSpec\Extension;


use commonsupport\PSB\Persistence\Doctrine2\SchemaHelper;
use commonsupport\PSB\Persistence\Doctrine2\StorageConfigCollection;
use Doctrine\DBAL\DriverManager;
use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ServiceContainer;
use specsupport\PSB\Persistence\Doctrine2\PhpSpec\Listener\DbalFixtureListener;
use specsupport\PSB\Persistence\Doctrine2\PhpSpec\Runner\Maintainer\DbalFixtureMaintainer;

class DbalFixtureExtension implements ExtensionInterface
{
    /**
     * @param ServiceContainer $container
     *
     * @throws \Exception
     */
    public function load(ServiceContainer $container)
    {
        $container->setShared(
            'dbal_fixture.options',
            function (ServiceContainer $container) {
                $options = $container->getParam('dbal_fixture_options');
                if (!$options) {
                    throw new \Exception('Cannot run tests without fixture options.');
                }

                if (!$options['connection_params']) {
                    throw new \Exception('Cannot run tests without database connection params.');
                }

                if (!$options['storage_configs']) {
                    throw new \Exception(
                        'Cannot run tests without knowing which table and schema to use for each storage type.'
                    );
                }

                return $options;
            }
        );

        $container->setShared(
            'dbal_fixture.connection',
            function (ServiceContainer $container) {
                $options = $container->get('dbal_fixture.options');
                return DriverManager::getConnection($options['connection_params']);
            }
        );

        $container->setShared(
            'dbal_fixture.schema_helper',
            function (ServiceContainer $container) {
                $options = (array)$container->get('dbal_fixture.options');
                $configCollection = StorageConfigCollection::fromArray($options['storage_configs']);
                return new SchemaHelper(
                    $container->get('dbal_fixture.connection'), $configCollection->asArray()
                );
            }
        );

        $container->setShared(
            'runner.maintainers.dbal_fixture',
            function (ServiceContainer $container) {
                return new DbalFixtureMaintainer(
                    $container->get('dbal_fixture.connection'),
                    $container->get('dbal_fixture.schema_helper')
                );
            }
        );

        $container->setShared(
            'event_dispatcher.listeners.dbal_fixture',
            function (ServiceContainer $container) {
                return new DbalFixtureListener($container->get('dbal_fixture.schema_helper'));
            }
        );
    }
}
