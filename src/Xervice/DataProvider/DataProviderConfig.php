<?php
declare(strict_types=1);


namespace Xervice\DataProvider;


use Xervice\Core\Business\Model\Config\AbstractConfig;
use Xervice\DataProvider\Business\Model\DataProvider\AbstractDataProvider;

class DataProviderConfig extends AbstractConfig
{
    public const DATA_PROVIDER_PATHS = 'dataprovider.data.provider.paths';

    public const DATA_PROVIDER_GENERATED_PATH = 'dataprovider.data.provider.generated.path';

    public const DATA_PROVIDER_NAMESPACE = 'dataprovider.data.provider.namespace';

    public const FILE_PATTERN = 'dataprovider.file.pattern';

    public const DATA_PROVIDER_EXTENDS = 'dataprovider.data.provider.extends';

    /**
     * @return string
     */
    public function getDataProviderExtends(): string
    {
        return $this->get(self::DATA_PROVIDER_EXTENDS, AbstractDataProvider::class);
    }

    /**
     * @return array
     */
    public function getPaths(): array
    {
        return $this->get(self::DATA_PROVIDER_PATHS, []);
    }

    /**
     * @return string
     */
    public function getGeneratedPath(): string
    {
        return $this->get(self::DATA_PROVIDER_GENERATED_PATH);
    }

    /**
     * @return string
     */
    public function getDataProviderNamespace(): string
    {
        return $this->get(self::DATA_PROVIDER_NAMESPACE, 'DataProvider');
    }

    /**
     * @return string
     */
    public function getFilePattern(): string
    {
        return $this->get(self::FILE_PATTERN, '*.dataprovider.xml');
    }
}