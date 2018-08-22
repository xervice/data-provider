<?php
declare(strict_types=1);

namespace Xervice\DataProvider\Business\Model\Cleaner;

interface CleanerInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function cleanGeneratedFiles(): void;
}