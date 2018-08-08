<?php
declare(strict_types=1);


namespace Xervice\DataProvider;


use Symfony\Component\DomCrawler\Crawler;
use Xervice\Core\Factory\AbstractFactory;
use Xervice\DataProvider\Cleaner\Cleaner;
use Xervice\DataProvider\Cleaner\CleanerInterface;
use Xervice\DataProvider\Finder\DataProviderFinder;
use Xervice\DataProvider\Finder\DataProviderFinderInterface;
use Xervice\DataProvider\Generator\DataProviderGenerator;
use Xervice\DataProvider\Generator\DataProviderGeneratorInterface;
use Xervice\DataProvider\Generator\FileWriter;
use Xervice\DataProvider\Generator\FileWriterInterface;
use Xervice\DataProvider\Parser\DataProviderParser;
use Xervice\DataProvider\Parser\DataProviderParserInterface;
use Xervice\DataProvider\Parser\XmlMerger;
use Xervice\DataProvider\Parser\XmlMergerInterface;

/**
 * @method \Xervice\DataProvider\DataProviderConfig getConfig()
 */
class DataProviderFactory extends AbstractFactory
{
    /**
     * @return \Xervice\DataProvider\Generator\DataProviderGenerator
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
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
     * @return \Xervice\DataProvider\Cleaner\Cleaner
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
     */
    public function createCleaner(): CleanerInterface
    {
        return new Cleaner(
            $this->createDataProviderFinder()
        );
    }

    /**
     * @return \Xervice\DataProvider\Parser\DataProviderParser
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
     */
    public function createDataProviderParser(): DataProviderParserInterface
    {
        return new DataProviderParser(
            $this->createDataProviderFinder(),
            $this->createXmlMerger()
        );
    }

    /**
     * @return \Xervice\DataProvider\Finder\DataProviderFinder
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
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
     * @return \Xervice\DataProvider\Parser\XmlMerger
     */
    public function createXmlMerger(): XmlMergerInterface
    {
        return new XmlMerger();
    }

    /**
     * @return \Xervice\DataProvider\Generator\FileWriter
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
     */
    public function createFileWriter(): FileWriterInterface
    {
        return new FileWriter(
            $this->getConfig()->getGeneratedPath()
        );
    }
}