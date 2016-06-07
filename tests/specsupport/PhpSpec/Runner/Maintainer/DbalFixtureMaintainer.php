<?php
namespace specsupport\PSB\Persistence\Doctrine2\PhpSpec\Runner\Maintainer;


use commonsupport\PSB\Persistence\Doctrine2\SchemaHelperInterface;
use Doctrine\DBAL\Connection;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\SpecificationInterface;
use specsupport\PSB\Persistence\Doctrine2\DatabaseAwareSpecInterface;

class DbalFixtureMaintainer implements MaintainerInterface
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
     * @param Connection                                                     $connection
     * @param \commonsupport\PSB\Persistence\Doctrine2\SchemaHelperInterface $schemaHelper
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
    public function supports(ExampleNode $example)
    {
        return $example->getSpecification()->getClassReflection()->implementsInterface(
            DatabaseAwareSpecInterface::class
        );
    }

    /**
     * @param ExampleNode            $example
     * @param SpecificationInterface $context
     * @param MatcherManager         $matchers
     * @param CollaboratorManager    $collaborators
     */
    public function prepare(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
        /** @var DatabaseAwareSpecInterface $context */
        $context->setConnection($this->connection);
        $context->setSchemaHelper($this->schemaHelper);
    }

    /**
     * @param ExampleNode            $example
     * @param SpecificationInterface $context
     * @param MatcherManager         $matchers
     * @param CollaboratorManager    $collaborators
     */
    public function teardown(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
    }

    /**
     * @return integer
     */
    public function getPriority()
    {
        return 100;
    }
}
