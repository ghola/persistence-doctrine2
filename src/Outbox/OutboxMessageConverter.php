<?php
namespace PSB\Persistence\Doctrine2\Outbox;


use PSB\Core\Outbox\OutboxMessage;
use PSB\Core\Outbox\OutboxTransportOperation;

class OutboxMessageConverter
{
    /**
     * @param array $dataArray
     *
     * @return OutboxMessage
     */
    public function fromDatabaseArray(array $dataArray)
    {
        $transportOperationsAsArrays = json_decode($dataArray['transport_operations'] ?: '[]', true);
        $transportOperationsAsObjects = [];
        foreach ($transportOperationsAsArrays as $transportOperation) {
            $transportOperationsAsObjects[] = new OutboxTransportOperation(
                $transportOperation['message_id'],
                $transportOperation['options'],
                $transportOperation['body'],
                $transportOperation['headers']
            );
        }

        return new OutboxMessage($dataArray['message_id'], $transportOperationsAsObjects);
    }

    /**
     * @param OutboxMessage $message
     *
     * @return array
     */
    public function toDatabaseArray(OutboxMessage $message)
    {
        $transportOperations = [];
        foreach ($message->getTransportOperations() as $transportOperation) {
            $transportOperations[] = [
                'message_id' => $transportOperation->getMessageId(),
                'body' => $transportOperation->getBody(),
                'headers' => $transportOperation->getHeaders(),
                'options' => $transportOperation->getOptions()
            ];
        }

        return [
            'message_id' => $message->getMessageId(),
            'transport_operations' => json_encode($transportOperations)
        ];
    }
}
