<?php
namespace commonsupport\PSB\Persistence\Doctrine2;


class StorageConfigCollection
{
    /**
     * @var StorageConfig[]
     */
    private $storageConfigs = [];

    /**
     * @param StorageConfig[] $storageConfigs
     */
    public function __construct(array $storageConfigs)
    {
        $this->storageConfigs = $storageConfigs;
    }

    /**
     * @param array $storageConfigsAsArray
     *
     * @return StorageConfigCollection
     */
    public static function fromArray(array $storageConfigsAsArray)
    {
        $storageConfigs = [];
        foreach ($storageConfigsAsArray as $storageTypeConstant => $tableConfigsAsArray) {
            $storageType = constant("PSB\\Core\\Persistence\\StorageType::$storageTypeConstant");
            $storageConfigs[$storageType] = StorageConfig::fromArray($storageType, $tableConfigsAsArray);
        }

        return new self($storageConfigs);
    }

    /**
     * @return StorageConfig[]
     */
    public function asArray()
    {
        return $this->storageConfigs;
    }
}
