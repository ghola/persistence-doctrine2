<?php

namespace specsupport\PSB\Persistence\Doctrine2\PhpSpec\Runner\Maintainer;


use commonsupport\PSB\Persistence\Doctrine2\SchemaHelperInterface;
use Doctrine\DBAL\Connection;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Runner\Maintainer\Maintainer;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Specification;
use specsupport\PSB\Persistence\Doctrine2\DatabaseAwareSpecInterface;

class DbalFixtureMaintainer implements Maintainer
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var SchemaHelperInterface
     */
    private $schemaHelper;

    /**
     * @param Connection            $connection
     * @param SchemaHelperInterface $schemaHelper
     */
    public function __construct(Connection $connection, SchemaHelperInterface $schemaHelper)
    {
        $this->connection = $connection;
        $this->schemaHelper = $schemaHelper;
    }

    /**
     * @param ExampleNode $example
     *
     * @return boolean
     */
    public function supports(ExampleNode $example): bool
    {
        return $example->getSpecification()->getClassReflection()->implementsInterface(
            DatabaseAwareSpecInterface::class
        );
    }

    /**
     * @param ExampleNode         $example
     * @param Specification       $context
     * @param MatcherManager      $matchers
     * @param CollaboratorManager $collaborators
     */
    public function prepare(
        ExampleNode $example,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
        /** @var DatabaseAwareSpecInterface $context */
        $context->setConnection($this->connection);
        $context->setSchemaHelper($this->schemaHelper);
    }

    /**
     * @param ExampleNode         $example
     * @param Specification       $context
     * @param MatcherManager      $matchers
     * @param CollaboratorManager $collaborators
     */
    public function teardown(
        ExampleNode $example,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
    }

    /**
     * @return integer
     */
    public function getPriority(): int
    {
        return 100;
    }
}
