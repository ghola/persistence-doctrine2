<?php
namespace PSB\Persistence\Doctrine2;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use PSB\Core\Exception\UnexpectedValueException;
use PSB\Core\Util\Settings;

class DbalConnectionFactory
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return Connection
     * @throws DBALException
     */
    public function __invoke()
    {
        $connection = $this->settings->tryGet(Doctrine2KnownSettingsEnum::CONNECTION);

        if ($connection) {
            return $connection;
        }

        $connectionParameters = $this->settings->tryGet(Doctrine2KnownSettingsEnum::CONNECTION_PARAMETERS);
        if (!$connectionParameters) {
            throw new UnexpectedValueException(
                "The Doctrine 2 persistence requires either a connection instance or connection parameters. " .
                "You can provide them through the persistence configurator."
            );
        }

        return DriverManager::getConnection($connectionParameters);
    }
}
