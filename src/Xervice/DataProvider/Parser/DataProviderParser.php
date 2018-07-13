<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Parser;


use Xervice\DataProvider\Finder\DataProviderFinder;

class DataProviderParser implements DataProviderParserInterface
{
    /**
     * @var \Xervice\DataProvider\Finder\DataProviderFinder
     */
    private $finder;

    /**
     * @var \Xervice\DataProvider\Parser\XmlMerger
     */
    private $xmlMerger;

    /**
     * DataProviderParser constructor.
     *
     * @param \Xervice\DataProvider\Finder\DataProviderFinder $finder
     * @param \Xervice\DataProvider\Parser\XmlMerger $xmlMerger
     */
    public function __construct(
        DataProviderFinder $finder,
        XmlMerger $xmlMerger
    ) {
        $this->finder = $finder;
        $this->xmlMerger = $xmlMerger;
    }


    /**
     * @return array
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getDataProvider() : array
    {
        foreach ($this->finder->getSchemaFiles() as $file) {
            $this->xmlMerger->addXml($file->getContents());
        }

        return $this->xmlMerger->getData();
    }
}