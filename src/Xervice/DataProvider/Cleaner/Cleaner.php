<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Cleaner;

use Xervice\DataProvider\Finder\DataProviderFinder;

class Cleaner implements CleanerInterface
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

    /**
     * @throws \InvalidArgumentException
     */
    public function cleanGeneratedFiles() : void
    {
        foreach ($this->finder->getGeneratedFiles() as $dataProviderFile) {
            unlink($dataProviderFile->getRealPath());
        }
    }


}