<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Business;
use Xervice\Core\Business\Model\Facade\AbstractFacade;
use Xervice\DataProvider\Business\Model\Parser\DataProviderParserInterface;


/**
 * @method \Xervice\DataProvider\Business\DataProviderBusinessFactory getFactory()
 */
class DataProviderFacade extends AbstractFacade
{
    /**
     * @return array
     * @throws \Nette\InvalidArgumentException
     */
    public function generateDataProvider(): array
    {
        return $this->getFactory()->createDataProviderGenerator()->generate();
    }

    /**
     * @param \Xervice\DataProvider\Business\Model\Parser\DataProviderParserInterface $dataProviderParser
     *
     * @return array
     */
    public function generateCustomDataProvider(DataProviderParserInterface $dataProviderParser): array
    {
        return $this
            ->getFactory()
            ->createCustomGenerator($dataProviderParser)
            ->generate();
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function cleanDataProvider(): void
    {
        $this->getFactory()->createCleaner()->cleanGeneratedFiles();
    }
}