<?php
namespace commonsupport\PSB\Persistence\Doctrine2;


interface SchemaHelperInterface
{
    /**
     * @param string $storageType
     */
    public function create($storageType);

    /**
     * @param string $storageType
     */
    public function drop($storageType);

    /**
     * @param string $storageType
     */
    public function clean($storageType);

    public function createAll();

    public function dropAll();

    public function cleanAll();

    /**
     * @param string $storageType
     * @param string $tableNameType
     *
     * @return string
     */
    public function getTableFor($storageType, $tableNameType);
}
