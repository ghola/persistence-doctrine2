<?php
namespace commonsupport\PSB\Persistence\Doctrine2;


class StorageConfig
{
    /**
     * @var string
     */
    private $storageType;

    /**
     * @var TableConfig[]
     */
    private $tableConfigs;

    /**
     * @param string        $storageType
     * @param TableConfig[] $tableConfigs
     */
    public function __construct($storageType, array $tableConfigs)
    {
        $this->storageType = $storageType;
        $this->tableConfigs = $tableConfigs;
    }

    /**
     * @param string $storageType
     * @param array  $tableConfigsAsArray
     *
     * @return StorageConfig
     */
    public static function fromArray($storageType, array $tableConfigsAsArray)
    {
        $tableConfigs = [];
        foreach ($tableConfigsAsArray as $tableNameTypeConstant => $tableConfigAsArray) {
            $tableNameType = constant(
                "PSB\\Persistence\\Doctrine2\\Doctrine2KnownSettingsEnum::$tableNameTypeConstant"
            );
            $tableConfigs[$tableNameType] = new TableConfig(
                $tableConfigAsArray['table_name'],
                new $tableConfigAsArray['schema_provider']
            );
        }
        return new StorageConfig($storageType, $tableConfigs);
    }

    /**
     * @return string
     */
    public function getStorageType()
    {
        return $this->storageType;
    }

    /**
     * @return TableConfig[]
     */
    public function getTableConfigs()
    {
        return $this->tableConfigs;
    }
}
