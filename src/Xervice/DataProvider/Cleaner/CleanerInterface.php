<?php
declare(strict_types=1);

namespace Xervice\DataProvider\Cleaner;

interface CleanerInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function cleanGeneratedFiles(): void;
}