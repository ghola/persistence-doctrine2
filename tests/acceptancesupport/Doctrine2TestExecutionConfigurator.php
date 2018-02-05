<?php
namespace acceptancesupport\PSB\Persistence\Doctrine2;


use acceptancesupport\PSB\Core\Scenario\EndpointTestExecutionConfiguratorInterface;
use commonsupport\PSB\Persistence\Doctrine2\SchemaHelper;
use commonsupport\PSB\Persistence\Doctrine2\StorageConfigCollection;
use Doctrine\DBAL\DriverManager;
use PSB\Core\EndpointConfigurator;
use PSB\Persistence\Doctrine2\Doctrine2PersistenceConfigurator;
use PSB\Persistence\Doctrine2\Doctrine2PersistenceDefinition;

class Doctrine2TestExecutionConfigurator implements EndpointTestExecutionConfiguratorInterface
{
    /**
     * @var array
     */
    private $connectionParams;

    /**
     * @var array
     */
    private $storageConfigs;

    /**
     * @param array  $connectionParams
     * @param string $storageConfigs
     */
    public function __construct(array $connectionParams = [], $storageConfigs)
    {
        $this->connectionParams = $connectionParams;
        $this->storageConfigs = json_decode($storageConfigs, true);
    }

    /**
     * @param EndpointConfigurator $endpointConfigurator
     */
    public function configure(EndpointConfigurator $endpointConfigurator)
    {
        /** @var Doctrine2PersistenceConfigurator $persistenceConfigurator */
        $persistenceConfigurator = $endpointConfigurator->usePersistence(new Doctrine2PersistenceDefinition());
        $persistenceConfigurator->useConnectionParameters($this->connectionParams);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function cleanup()
    {
        $connection = DriverManager::getConnection($this->connectionParams);
        $configCollection = StorageConfigCollection::fromArray($this->storageConfigs);
        $schemaHelper = new SchemaHelper($connection, $configCollection->asArray());
        $schemaHelper->dropAll();
        $connection->close();
    }
}
