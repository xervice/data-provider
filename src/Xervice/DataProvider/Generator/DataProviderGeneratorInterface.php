<?php
declare(strict_types=1);

namespace Xervice\DataProvider\Generator;

interface DataProviderGeneratorInterface
{
    /**
     * @return array
     */
    public function generate(): array;
}