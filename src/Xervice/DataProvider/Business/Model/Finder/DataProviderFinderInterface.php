<?php
declare(strict_types=1);

namespace Xervice\DataProvider\Business\Model\Finder;

use Symfony\Component\Finder\Finder;

interface DataProviderFinderInterface
{
    /**
     * @return \Symfony\Component\Finder\Finder
     * @throws \InvalidArgumentException
     */
    public function getSchemaFiles(): Finder;

    /**
     * @return \Symfony\Component\Finder\Finder
     * @throws \InvalidArgumentException
     */
    public function getGeneratedFiles(): Finder;
}