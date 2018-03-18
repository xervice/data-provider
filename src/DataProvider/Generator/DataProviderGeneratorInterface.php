<?php

namespace Xervice\DataProvider\Generator;

interface DataProviderGeneratorInterface
{
    /**
     * @return array
     */
    public function generate(): array;
}