<?php

namespace specsupport\PSB\Persistence\Doctrine2\PhpSpec\Extension;


use commonsupport\PSB\Persistence\Doctrine2\SchemaHelper;
use commonsupport\PSB\Persistence\Doctrine2\StorageConfigCollection;
use Doctrine\DBAL\DriverManager;
use PhpSpec\Extension;
use PhpSpec\ServiceContainer;
use specsupport\PSB\Persistence\Doctrine2\PhpSpec\Listener\DbalFixtureListener;
use specsupport\PSB\Persistence\Doctrine2\PhpSpec\Runner\Maintainer\DbalFixtureMaintainer;

class DbalFixtureExtension implements Extension
{
    /**
     * @param ServiceContainer $container
     * @param array            $params
     */
    public function load(ServiceContainer $container, array $params)
    {
        $container->define(
            'dbal_fixture.options',
            function () use ($params) {
                if (!$params) {
                    throw new \Exception('Cannot run tests without fixture options.');
                }

                if (!$params['connection_params']) {
                    throw new \Exception('Cannot run tests without database connection params.');
                }

                if (!$params['storage_configs']) {
                    throw new \Exception(
                        'Cannot run tests without knowing which table and schema to use for each storage type.'
                    );
                }

                return $params;
            }
        );

        $container->define(
            'dbal_fixture.connection',
            function (ServiceContainer $container) {
                $options = $container->get('dbal_fixture.options');
                return DriverManager::getConnection($options['connection_params']);
            }
        );

        $container->define(
            'dbal_fixture.schema_helper',
            function (ServiceContainer $container) {
                $options = (array)$container->get('dbal_fixture.options');
                $configCollection = StorageConfigCollection::fromArray($options['storage_configs']);
                return new SchemaHelper(
                    $container->get('dbal_fixture.connection'), $configCollection->asArray()
                );
            }
        );

        $container->define(
            'runner.maintainers.dbal_fixture',
            function (ServiceContainer $container) {
                return new DbalFixtureMaintainer(
                    $container->get('dbal_fixture.connection'),
                    $container->get('dbal_fixture.schema_helper')
                );
            },
            ['runner.maintainers']
        );

        $container->define(
            'event_dispatcher.listeners.dbal_fixture',
            function (ServiceContainer $container) {
                return new DbalFixtureListener($container->get('dbal_fixture.schema_helper'));
            },
            ['event_dispatcher.listeners']
        );
    }
}
