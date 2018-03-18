<?php


namespace Xervice\DataProvider;


use Xervice\Core\Config\AbstractConfig;

class DataProviderConfig extends AbstractConfig
{
    const DATA_PROVIDER_PATHS = 'data.provider.paths';

    const DATA_PROVIDER_GENERATED_PATH = 'data.provider.generated.path';

    /**
     * @return array
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function getPaths()
    {
        return $this->get(self::DATA_PROVIDER_PATHS, []);
    }

    /**
     * @return string
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function getGeneratedPath()
    {
        return $this->get(self::DATA_PROVIDER_GENERATED_PATH);
    }
}