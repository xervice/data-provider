<?php
declare(strict_types=1);

namespace Xervice\DataProvider\Business\Model\Parser;

interface XmlMergerInterface
{
    /**
     * @param string $xml
     */
    public function addXml(string $xml);

    /**
     * @return array
     */
    public function getData(): array;
}