<?php
declare(strict_types=1);

namespace Xervice\DataProvider\Business\Model\Parser;

interface DataProviderParserInterface
{
    /**
     * @return array
     */
    public function getDataProvider(): array;
}