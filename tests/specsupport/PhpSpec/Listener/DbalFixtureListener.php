<?php
namespace specsupport\PSB\Persistence\Doctrine2\PhpSpec\Listener;


use commonsupport\PSB\Persistence\Doctrine2\SchemaHelperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DbalFixtureListener implements EventSubscriberInterface
{
    /**
     * @var SchemaHelperInterface
     */
    private $schemaHelper;

    /**
     * @param SchemaHelperInterface $schemaHelper
     */
    public function __construct(SchemaHelperInterface $schemaHelper)
    {
        $this->schemaHelper = $schemaHelper;
    }

    public function ensureDatabase()
    {
        $this->schemaHelper->dropAll();
        $this->schemaHelper->createAll();
    }

    public function dropDatabase()
    {
        $this->schemaHelper->dropAll();
    }

    public static function getSubscribedEvents()
    {
        return [
            'beforeSuite' => 'ensureDatabase',
            'afterSuite' => 'dropDatabase'
        ];
    }
}
