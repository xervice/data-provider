<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Finder;


use Symfony\Component\Finder\Finder;
use Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable;

class DataProviderFinder implements DataProviderFinderInterface
{
    /**
     * @var array
     */
    private $paths;

    /**
     * @var string
     */
    private $generatedPath;

    /**
     * DataProviderFinder constructor.
     *
     * @param array $paths
     * @param string $generatedPath
     *
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
     */
    public function __construct(array $paths, string $generatedPath)
    {
        $this->paths = $paths;
        $this->generatedPath = $generatedPath;

        if (
            !is_writable($this->generatedPath)
            && !mkdir($this->generatedPath, '0777', true)
            && !is_dir($this->generatedPath)
        ) {
            throw new GenerateDirectoryNotWriteable($this->generatedPath);
        }
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     * @throws \InvalidArgumentException
     */
    public function getSchemaFiles() : Finder
    {
        $finder = new Finder();

        $finder->files()->name('*.dataprovider.xml')->sortByName();
        $finder->in($this->paths);

        return $finder;
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     * @throws \InvalidArgumentException
     */
    public function getGeneratedFiles() : Finder
    {
        $finder = new Finder();

        $finder->files()->name('*DataProvider.php')->sortByName();
        $finder->in($this->generatedPath);

        return $finder;
    }
}