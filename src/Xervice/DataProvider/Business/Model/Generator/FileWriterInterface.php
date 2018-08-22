<?php
declare(strict_types=1);

namespace Xervice\DataProvider\Business\Model\Generator;

interface FileWriterInterface
{
    /**
     * @param string $filename
     * @param string $content
     */
    public function writeToFile(string $filename, string $content);
}