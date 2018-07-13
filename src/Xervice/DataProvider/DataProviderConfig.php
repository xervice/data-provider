<?php
declare(strict_types=1);


namespace Xervice\DataProvider;


use Xervice\Core\Config\AbstractConfig;

class DataProviderConfig extends AbstractConfig
{
    public const DATA_PROVIDER_PATHS = 'data.provider.paths';

    public const DATA_PROVIDER_GENERATED_PATH = 'data.provider.generated.path';

    public const DATA_PROVIDER_NAMESPACE = 'data.provider.namespace';

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
}