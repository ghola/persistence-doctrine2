<?php
namespace commonsupport\PSB\Persistence\Doctrine2;


use Doctrine\DBAL\Connection;

class ConnectionKiller
{
    public static function killConection(Connection $connection)
    {
        $driverName = $connection->getDriver()->getName();
        if (stripos($driverName, 'mysql') !== false) {
            static::killMysqlConnection($connection);
        }
    }

    public static function killMysqlConnection(Connection $connection)
    {
        try {
            $connection->executeQuery("KILL CONNECTION_ID()");
        } catch (\Exception $e) {
        }

        /**
         * Looping because just killing does not guarantee it gets killed
         * as per http://dev.mysql.com/doc/refman/5.6/en/kill.html
         */
        $isKilled = false;
        while (!$isKilled) {
            try {
                $connection->executeQuery("SELECT * FROM mysql.help_topic ORDER BY help_topic_id DESC");
            } catch (\Exception $e) {
                $isKilled = true;
            }
        }
    }
}
