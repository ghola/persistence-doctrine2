<?php
namespace PSB\Persistence\Doctrine2\Outbox;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use PSB\Core\Exception\CriticalErrorException;

class OutboxPersister
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $endpointsTableName;

    /**
     * @var string
     *
     */
    private $messagesTableName;

    /**
     * @param Connection $connection
     * @param string     $messagesTableName
     * @param string     $endpointsTableName
     */
    public function __construct(
        Connection $connection,
        $messagesTableName,
        $endpointsTableName
    ) {
        $this->connection = $connection;
        $this->endpointsTableName = $endpointsTableName;
        $this->messagesTableName = $messagesTableName;
    }

    /**
     * @param int    $endpointId
     * @param string $messageId
     *
     * @return array
     */
    public function get($endpointId, $messageId)
    {
        try {
            $result = $this->connection->executeQuery(
                "SELECT * FROM {$this->messagesTableName} WHERE endpoint_id = ? AND message_id = ?",
                [$endpointId, hex2bin($this->stripDashes($messageId))]
            )->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $this->attemptToReconnectPresumedLostConnection($e);
        }

        if (!$result) {
            return null;
        }

        unset($result['id']);
        unset($result['dispatched_at']);
        unset($result['endpoint_id']);
        $result['message_id'] = bin2hex($result['message_id']);

        return $result;
    }

    /**
     * @param int   $endpointId
     * @param array $outboxRecord
     *
     * @throws \Exception
     */
    public function store($endpointId, array $outboxRecord)
    {
        $outboxRecord['message_id'] = hex2bin($this->stripDashes($outboxRecord['message_id']));
        $outboxRecord['endpoint_id'] = $endpointId;
        $outboxRecord['is_dispatched'] = 0;

        $this->connection->transactional(
            function (Connection $connection) use ($outboxRecord) {
                $connection->insert($this->messagesTableName, $outboxRecord);
            }
        );
    }

    /**
     * @param int    $endpointId
     * @param string $messageId
     *
     * @throws \Exception
     */
    public function markAsDispatched($endpointId, $messageId)
    {
        try {
            $this->connection->executeUpdate(
                "UPDATE {$this->messagesTableName}
                     SET is_dispatched = 1, dispatched_at = ?, transport_operations = ''
                     WHERE endpoint_id = ? AND message_id = ? AND is_dispatched = 0",
                [
                    (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
                    $endpointId,
                    hex2bin($this->stripDashes($messageId))
                ]
            );
        } catch (\Exception $e) {
            throw $this->attemptToReconnectPresumedLostConnection($e);
        }
    }

    /**
     * Initiates the transaction
     * Attempts to reconnect once if disconnected.
     *
     * @return void
     */
    public function beginTransaction()
    {
        try {
            $this->connection->beginTransaction();
        } catch (\Exception $e) {
            throw $this->attemptToReconnectPresumedLostConnection($e);
        }
    }

    /**
     * Commits the transaction
     *
     * Does not attempt to reconnect if disconnected because the transaction would be broken anyway.
     * Reconnection should be done by rollBack.
     *
     * @return void
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * Rolls back the transaction.
     * It makes sure that the connection is in the correct state regardless of what happened before.
     * Correct state means that connection is not rollback only and does not have a transaction nesting level > 0
     *
     * @throws \Exception
     */
    public function rollBack()
    {
        try {
            /**
             * Roll back all the way as this is supposed to be the top level transaction and we want to reset
             * the nesting level
             */
            $transactionNestingLevel = $this->connection->getTransactionNestingLevel();
            for ($i = 0; $i < $transactionNestingLevel - 1; $i++) {
                $this->connection->rollBack();
            }
            $this->connection->rollBack();
        } catch (\Exception $e) {
            $rethrowable = $this->attemptToReconnectPresumedLostConnection($e);
            /**
             * If connection is functional we need to make sure the connection is not rollback only.
             * This can only be achieved by starting a transaction and rolling it back (the "why" is found in
             * lines 1277-1279 of Doctrine\DBAL\Connection).
             */
            if ($rethrowable === $e) {
                $this->connection->beginTransaction();
                $this->connection->rollBack();
            }
            throw $rethrowable;
        }
    }

    /**
     * Attempts to reconnect once if disconnected.
     *
     * @param \DateTime $dateTime
     *
     * @throws ConnectionException
     * @throws \Exception
     */
    public function removeEntriesOlderThan(\DateTime $dateTime)
    {
        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        $this->connection->executeUpdate(
            "DELETE FROM {$this->messagesTableName} WHERE is_dispatched = 1 AND dispatched_at <= ?",
            [$dateTime->format('Y-m-d H:i:s')]
        );
    }

    /**
     * @param string $endpointName
     *
     * @return int
     */
    public function fetchOrGenerateEndpointId($endpointName)
    {
        $endpointId = 0;
        $this->connection->transactional(
            function (Connection $connection) use ($endpointName, &$endpointId) {
                $lookupHash = md5($endpointName);
                $endpointRecord = $connection->executeQuery(
                    "SELECT * FROM {$this->endpointsTableName} WHERE lookup_hash = ?",
                    [hex2bin($lookupHash)]
                )->fetch(\PDO::FETCH_ASSOC);
                if (!$endpointRecord) {
                    $connection->insert(
                        $this->endpointsTableName,
                        ['lookup_hash' => hex2bin($lookupHash), 'name' => $endpointName]
                    );
                    $endpointId = (int)$connection->lastInsertId();
                } else {
                    $endpointId = (int)$endpointRecord['id'];
                }

            }
        );

        return $endpointId;
    }

    /**
     * @param string $messageId
     *
     * @return string
     */
    private function stripDashes($messageId)
    {
        return str_replace('-', '', $messageId);
    }

    /**
     * It attempts to reconnect if connection is non responsive. Failing to reconnect triggers a critical error.
     * If connection is responsive or successfully reconnected it rethrows, relying on the bus retries
     * to re-execute everything from the beginning.
     *
     * @param \Exception $e
     *
     * @return \Exception|CriticalErrorException
     */
    private function attemptToReconnectPresumedLostConnection(\Exception $e)
    {
        // presumably, any exception caught here is related to some connection error
        if (!$this->connection->ping()) {
            // if pinging fails, we try to reconnect
            try {
                $this->connection->close();
                $this->connection->connect();
            } catch (\Exception $e) {
                // if reconnecting fails, there is no way that the bus can continue to function
                return new CriticalErrorException("Database connection failed.", 0, $e);
            }
        }

        return $e;
    }
}
