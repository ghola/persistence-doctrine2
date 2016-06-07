<?php
namespace PSB\Persistence\Doctrine2;


class Doctrine2KnownSettingsEnum
{
    const CONNECTION_PARAMETERS = 'PSB.Doctrine2.ConnectionParameters';
    const CONNECTION = 'PSB.Doctrine2.DbalConnection';
    const OUTBOX_ENDPOINT_ID = 'PSB.Doctrine2.Outbox.EndpointId';
    const OUTBOX_MESSAGES_TABLE_NAME = 'PSB.Doctrine2.Outbox.MessagesTableName';
    const OUTBOX_ENDPOINTS_TABLE_NAME = 'PSB.Doctrine2.Outbox.EndpointsTableName';
}
