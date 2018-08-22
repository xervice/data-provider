<?php
declare(strict_types=1);

namespace Xervice\DataProvider\Business\Model\Generator;

interface DataProviderGeneratorInterface
{
    /**
     * @return array
     */
    public function generate(): array;
}