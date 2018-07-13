<?php
declare(strict_types=1);


namespace Xervice\DataProvider;


use Xervice\Core\Facade\AbstractFacade;

/**
 * @method \Xervice\DataProvider\DataProviderFactory getFactory()
 */
class DataProviderFacade extends AbstractFacade
{
    /**
     * @return array
     * @throws \Nette\InvalidArgumentException
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
     */
    public function generateDataProvider(): array
    {
        return $this->getFactory()->createDataProviderGenerator()->generate();
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
     */
    public function cleanDataProvider(): void
    {
        $this->getFactory()->createCleaner()->cleanGeneratedFiles();
    }
}