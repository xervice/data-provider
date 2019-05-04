<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Business;


use Xervice\Core\Business\Model\Factory\AbstractBusinessFactory;
use Xervice\DataProvider\Business\Model\Cleaner\Cleaner;
use Xervice\DataProvider\Business\Model\Cleaner\CleanerInterface;
use Xervice\DataProvider\Business\Model\Finder\DataProviderFinder;
use Xervice\DataProvider\Business\Model\Finder\DataProviderFinderInterface;
use Xervice\DataProvider\Business\Model\Generator\DataProviderGenerator;
use Xervice\DataProvider\Business\Model\Generator\DataProviderGeneratorInterface;
use Xervice\DataProvider\Business\Model\Generator\FileWriter;
use Xervice\DataProvider\Business\Model\Generator\FileWriterInterface;
use Xervice\DataProvider\Business\Model\Parser\DataProviderParser;
use Xervice\DataProvider\Business\Model\Parser\DataProviderParserInterface;
use Xervice\DataProvider\Business\Model\Parser\XmlMerger;
use Xervice\DataProvider\Business\Model\Parser\XmlMergerInterface;

/**
 * @method \Xervice\DataProvider\DataProviderConfig getConfig()
 */
class DataProviderBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @param \Xervice\DataProvider\Business\Model\Parser\DataProviderParserInterface $dataProviderParser
     *
     * @return \Xervice\DataProvider\Business\Model\Generator\DataProviderGeneratorInterface
     */
    public function createCustomGenerator(DataProviderParserInterface $dataProviderParser): DataProviderGeneratorInterface
    {
        return new DataProviderGenerator(
            $dataProviderParser,
            $this->createFileWriter(),
            $this->getConfig()->getDataProviderNamespace(),
            $this->getConfig()->getDataProviderExtends()
        );
    }

    /**
     * @return \Xervice\DataProvider\Business\Model\Generator\DataProviderGenerator
     */
    public function createDataProviderGenerator(): DataProviderGeneratorInterface
    {
        return new DataProviderGenerator(
            $this->createDataProviderParser(),
            $this->createFileWriter(),
            $this->getConfig()->getDataProviderNamespace(),
            $this->getConfig()->getDataProviderExtends()
        );
    }

    /**
     * @return \Xervice\DataProvider\Business\Model\Cleaner\Cleaner
     */
    public function createCleaner(): CleanerInterface
    {
        return new Cleaner(
            $this->createDataProviderFinder()
        );
    }

    /**
     * @return \Xervice\DataProvider\Business\Model\Parser\DataProviderParser
     */
    public function createDataProviderParser(): DataProviderParserInterface
    {
        return new DataProviderParser(
            $this->createDataProviderFinder(),
            $this->createXmlMerger()
        );
    }

    /**
     * @return \Xervice\DataProvider\Business\Model\Finder\DataProviderFinder
     */
    public function createDataProviderFinder(): DataProviderFinderInterface
    {
        return new DataProviderFinder(
            $this->getConfig()->getPaths(),
            $this->getConfig()->getGeneratedPath(),
            $this->getConfig()->getFilePattern()
        );
    }

    /**
     * @return \Xervice\DataProvider\Business\Model\Parser\XmlMerger
     */
    public function createXmlMerger(): XmlMergerInterface
    {
        return new XmlMerger(
            $this->getConfig()->getDataProviderNamespace()
        );
    }

    /**
     * @return \Xervice\DataProvider\Business\Model\Generator\FileWriter
     */
    public function createFileWriter(): FileWriterInterface
    {
        return new FileWriter(
            $this->getConfig()->getGeneratedPath()
        );
    }
}