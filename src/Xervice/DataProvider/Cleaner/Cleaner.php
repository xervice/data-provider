<?php


namespace Xervice\DataProvider\Cleaner;

use Xervice\DataProvider\Finder\DataProviderFinder;

class Cleaner
{
    /**
     * @var \Xervice\DataProvider\Finder\DataProviderFinder
     */
    private $finder;

    /**
     * Cleaner constructor.
     *
     * @param \Xervice\DataProvider\Finder\DataProviderFinder $finder
     */
    public function __construct(DataProviderFinder $finder)
    {
        $this->finder = $finder;
    }

    public function cleanGeneratedFiles() : void
    {
        foreach ($this->finder->getGeneratedFiles() as $dataProviderFile) {
            unlink($dataProviderFile->getRealPath());
        }
    }


}