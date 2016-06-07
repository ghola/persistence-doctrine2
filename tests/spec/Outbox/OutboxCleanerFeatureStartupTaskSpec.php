<?php

namespace spec\PSB\Persistence\Doctrine2\Outbox;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PSB\Core\BusContextInterface;
use PSB\Core\Exception\UnexpectedValueException;
use PSB\Persistence\Doctrine2\Outbox\OutboxCleanerFeatureStartupTask;
use PSB\Persistence\Doctrine2\Outbox\OutboxPersister;

/**
 * @mixin OutboxCleanerFeatureStartupTask
 */
class OutboxCleanerFeatureStartupTaskSpec extends ObjectBehavior
{
    function it_is_initializable(OutboxPersister $outboxPersister, \DateTime $now, $daysToKeepDeduplicationData)
    {
        $this->beConstructedWith($outboxPersister, $now, $daysToKeepDeduplicationData);
        $this->shouldHaveType('PSB\Persistence\Doctrine2\Outbox\OutboxCleanerFeatureStartupTask');
    }

    function it_removes_entries_older_than_the_number_of_days_to_keep_deduplication_data(
        OutboxPersister $outboxPersister,
        BusContextInterface $busContext
    ) {
        $daysToKeepDeduplicationData = 5;
        $now = new \DateTime('2016-03-08T09:36:09Z');
        $this->beConstructedWith($outboxPersister, $now, $daysToKeepDeduplicationData);

        $outboxPersister->removeEntriesOlderThan(new \DateTime('2016-03-03T09:36:09Z'))->shouldBeCalled();

        $this->start($busContext);
    }

    function it_removes_entries_older_than_7_days_if_days_to_keep_deduplication_data_is_not_set(
        OutboxPersister $outboxPersister,
        BusContextInterface $busContext
    ) {
        $now = new \DateTime('2016-03-08T09:36:09Z');
        $this->beConstructedWith($outboxPersister, $now);

        $outboxPersister->removeEntriesOlderThan(new \DateTime('2016-03-01T09:36:09Z'))->shouldBeCalled();

        $this->start($busContext);
    }

    function it_throws_if_days_to_keep_deduplication_data_is_not_positive_integer(
        OutboxPersister $outboxPersister,
        BusContextInterface $busContext
    ) {
        $daysToKeepDeduplicationData = '-5';
        $now = new \DateTime('2016-03-08T09:36:09Z');
        $this->beConstructedWith($outboxPersister, $now, $daysToKeepDeduplicationData);

        $this->shouldThrow(
            new UnexpectedValueException(
                "Invalid value value used for days to keep deduplication data. Please ensure it is a positive integer."
            )
        )->duringStart($busContext);
    }
}
