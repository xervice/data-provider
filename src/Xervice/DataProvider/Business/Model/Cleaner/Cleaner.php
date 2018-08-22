<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Business\Model\Cleaner;

use Xervice\DataProvider\Business\Model\Finder\DataProviderFinder;

class Cleaner implements CleanerInterface
{
    /**
     * @var \Xervice\DataProvider\Business\Model\Finder\DataProviderFinder
     */
    private $finder;

    /**
     * Cleaner constructor.
     *
     * @param \Xervice\DataProvider\Business\Model\Finder\DataProviderFinder $finder
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