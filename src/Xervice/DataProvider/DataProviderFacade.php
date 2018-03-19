<?php


namespace Xervice\DataProvider;


use Xervice\Core\Facade\AbstractFacade;

/**
 * @method \Xervice\DataProvider\DataProviderFactory getFactory()
 */
class DataProviderFacade extends AbstractFacade
{
    /**
     * @return array
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function generateDataProvider()
    {
        return $this->getFactory()->createDataProviderGenerator()->generate();
    }

    /**
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function cleanDataProvider()
    {
        $this->getFactory()->createCleaner()->cleanGeneratedFiles();
    }
}