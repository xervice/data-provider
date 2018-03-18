<?php


namespace Xervice\DataProvider;


use Symfony\Component\DomCrawler\Crawler;
use Xervice\Core\Factory\AbstractFactory;
use Xervice\DataProvider\Cleaner\Cleaner;
use Xervice\DataProvider\Finder\DataProviderFinder;
use Xervice\DataProvider\Generator\DataProviderGenerator;
use Xervice\DataProvider\Generator\FileWriter;
use Xervice\DataProvider\Parser\DataProviderParser;
use Xervice\DataProvider\Parser\XmlMerger;

/**
 * @method \Xervice\DataProvider\DataProviderConfig getConfig()
 */
class DataProviderFactory extends AbstractFactory
{
    /**
     * @return \Xervice\DataProvider\Generator\DataProviderGenerator
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function createDataProviderGenerator()
    {
        return new DataProviderGenerator(
            $this->createDataProviderParser(),
            $this->createFileWriter()
        );
    }

    /**
     * @return \Xervice\DataProvider\Cleaner\Cleaner
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function createCleaner()
    {
        return new Cleaner(
            $this->createDataProviderFinder()
        );
    }

    /**
     * @return \Xervice\DataProvider\Parser\DataProviderParser
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function createDataProviderParser()
    {
        return new DataProviderParser(
            $this->createDataProviderFinder(),
            $this->createXmlMerger()
        );
    }

    /**
     * @return \Xervice\DataProvider\Finder\DataProviderFinder
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function createDataProviderFinder()
    {
        return new DataProviderFinder(
            $this->getConfig()->getPaths(),
            $this->getConfig()->getGeneratedPath()
        );
    }

    /**
     * @return \Xervice\DataProvider\Parser\XmlMerger
     */
    public function createXmlMerger()
    {
        return new XmlMerger();
    }

    /**
     * @return \Xervice\DataProvider\Generator\FileWriter
     * @throws \Xervice\Config\Exception\ConfigNotFound
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
     */
    public function createFileWriter()
    {
        return new FileWriter(
            $this->getConfig()->getGeneratedPath()
        );
    }
}