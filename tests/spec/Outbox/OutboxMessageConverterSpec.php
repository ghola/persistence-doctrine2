<?php

namespace spec\PSB\Persistence\Doctrine2\Outbox;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PSB\Core\Outbox\OutboxMessage;
use PSB\Core\Outbox\OutboxTransportOperation;
use PSB\Persistence\Doctrine2\Outbox\OutboxMessageConverter;

/**
 * @mixin OutboxMessageConverter
 */
class OutboxMessageConverterSpec extends ObjectBehavior
{
    /**
     * @var array
     */
    private $databaseRecord;

    /**
     * @var OutboxMessage
     */
    private $outboxMessage;

    function let()
    {
        $this->databaseRecord = [
            'message_id' => 'incomingmessageid',
            'transport_operations' => json_encode(
                [
                    [
                        'message_id' => 'outgoingid1',
                        'body' => 'body1',
                        'headers' => ['some1' => 'header1'],
                        'options' => ['some1' => 'option1']
                    ],
                    [
                        'message_id' => 'outgoingid2',
                        'body' => 'body2',
                        'headers' => ['some2' => 'header2'],
                        'options' => ['some2' => 'option2']
                    ]
                ]
            )
        ];

        $this->outboxMessage = new OutboxMessage(
            'incomingmessageid',
            [
                new OutboxTransportOperation(
                    'outgoingid1', ['some1' => 'option1'], 'body1', ['some1' => 'header1']
                ),
                new OutboxTransportOperation('outgoingid2', ['some2' => 'option2'], 'body2', ['some2' => 'header2'])
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PSB\Persistence\Doctrine2\Outbox\OutboxMessageConverter');
    }

    function it_converts_a_database_array_record_to_an_outbox_message()
    {
        $this->fromDatabaseArray($this->databaseRecord)->shouldBeLike($this->outboxMessage);
    }

    function it_converts_a_database_array_record_even_if_transport_operations_are_empty()
    {
        $this->fromDatabaseArray(['message_id' => 'irrelevant', 'transport_operations' => ''])->shouldBeLike(
            new OutboxMessage('irrelevant', [])
        );
    }

    function it_converts_an_outbox_message_to_an_database_array_record()
    {
        $this->toDatabaseArray($this->outboxMessage)->shouldReturn($this->databaseRecord);
    }
}
