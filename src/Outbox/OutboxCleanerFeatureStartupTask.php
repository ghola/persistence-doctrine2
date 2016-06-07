<?php
namespace PSB\Persistence\Doctrine2\Outbox;


use PSB\Core\BusContextInterface;
use PSB\Core\Exception\UnexpectedValueException;
use PSB\Core\Feature\FeatureStartupTaskInterface;

class OutboxCleanerFeatureStartupTask implements FeatureStartupTaskInterface
{
    /**
     * @var OutboxPersister
     */
    private $outboxPersister;

    /**
     * @var \DateTime
     */
    private $now;

    /**
     * @var int|null
     */
    private $daysToKeepDeduplicationData;

    /**
     * @param OutboxPersister $outboxPersister
     * @param \DateTime       $now
     * @param int|null        $daysToKeepDeduplicationData
     */
    public function __construct(OutboxPersister $outboxPersister, \DateTime $now, $daysToKeepDeduplicationData = null)
    {
        $this->outboxPersister = $outboxPersister;
        $this->now = $now;
        $this->daysToKeepDeduplicationData = $daysToKeepDeduplicationData;
    }

    /**
     * @param BusContextInterface $busContext
     *
     * @throws UnexpectedValueException
     */
    public function start(BusContextInterface $busContext)
    {
        if ($this->daysToKeepDeduplicationData === null) {
            $this->daysToKeepDeduplicationData = 7;
        } elseif (!ctype_digit((string)$this->daysToKeepDeduplicationData)) {
            throw new UnexpectedValueException(
                "Invalid value value used for days to keep deduplication data. Please ensure it is a positive integer."
            );
        }

        $this->outboxPersister->removeEntriesOlderThan(
            $this->now->sub(new \DateInterval("P{$this->daysToKeepDeduplicationData}D"))
        );
    }
}
