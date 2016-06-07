<?php
namespace PSB\Persistence\Doctrine2;


use Doctrine\DBAL\Connection;
use PSB\Core\Persistence\PersistenceConfigurator;

class Doctrine2PersistenceConfigurator extends PersistenceConfigurator
{
    /**
     * The connection parameters are the same as described in the Doctrine DBAL documentation
     *
     * @see http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
     *
     * @param array $connectionParameters
     *
     * @return Doctrine2PersistenceConfigurator
     */
    public function useConnectionParameters(array $connectionParameters)
    {
        $this->settings->set(Doctrine2KnownSettingsEnum::CONNECTION_PARAMETERS, $connectionParameters);
        return $this;
    }

    /**
     * Instead of the connection parameters one can use an already set up connection object.
     *
     * @param Connection $dbalConnection
     *
     * @return Doctrine2PersistenceConfigurator
     */
    public function useConnection(Connection $dbalConnection)
    {
        $this->settings->set(Doctrine2KnownSettingsEnum::CONNECTION, $dbalConnection);
        return $this;
    }

    /**
     * @param string $tableName
     *
     * @return Doctrine2PersistenceConfigurator
     */
    public function useOutboxMessagesTableName($tableName)
    {
        $this->settings->set(Doctrine2KnownSettingsEnum::OUTBOX_MESSAGES_TABLE_NAME, $tableName);
        return $this;
    }

    /**
     * @param string $tableName
     *
     * @return Doctrine2PersistenceConfigurator
     */
    public function useOutboxEndpointsTableName($tableName)
    {
        $this->settings->set(Doctrine2KnownSettingsEnum::OUTBOX_ENDPOINTS_TABLE_NAME, $tableName);
        return $this;
    }
}
