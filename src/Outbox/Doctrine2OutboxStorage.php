<?php
namespace PSB\Persistence\Doctrine2\Outbox;


use PSB\Core\Outbox\OutboxMessage;
use PSB\Core\Outbox\OutboxStorageInterface;

class Doctrine2OutboxStorage implements OutboxStorageInterface
{
    /**
     * @var OutboxPersister
     */
    private $persister;

    /**
     * @var OutboxMessageConverter
     */
    private $messageConverter;

    /**
     * @var int
     */
    private $endpointId;

    /**
     * @param OutboxPersister        $persister
     * @param OutboxMessageConverter $messageConverter
     * @param int                    $endpointId
     */
    public function __construct(OutboxPersister $persister, OutboxMessageConverter $messageConverter, $endpointId)
    {
        $this->persister = $persister;
        $this->messageConverter = $messageConverter;
        $this->endpointId = $endpointId;
    }

    /**
     * Fetches the given message from the storage. It returns null if no message is found.
     *
     * @param string $messageId
     *
     * @return OutboxMessage|null
     */
    public function get($messageId)
    {
        $outboxRecord = $this->persister->get($this->endpointId, $messageId);
        if ($outboxRecord) {
            return $this->messageConverter->fromDatabaseArray($outboxRecord);
        }
        return null;
    }

    /**
     * Stores the message to enable deduplication and re-dispatching of transport operations.
     * Throws an exception if a message with the same ID already exists.
     *
     * @param OutboxMessage $message
     *
     * @return void
     */
    public function store(OutboxMessage $message)
    {
        $outboxRecord = $this->messageConverter->toDatabaseArray($message);
        $this->persister->store($this->endpointId, $outboxRecord);
    }

    /**
     * @param string $messageId
     *
     * @return void
     */
    public function markAsDispatched($messageId)
    {
        $this->persister->markAsDispatched($this->endpointId, $messageId);
    }

    /**
     * Initiates the transaction
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->persister->beginTransaction();
    }

    /**
     * Commits the transaction
     *
     * @return void
     */
    public function commit()
    {
        $this->persister->commit();
    }

    /**
     * Rolls back the transaction
     *
     * @return void
     */
    public function rollBack()
    {
        $this->persister->rollBack();
    }
}
