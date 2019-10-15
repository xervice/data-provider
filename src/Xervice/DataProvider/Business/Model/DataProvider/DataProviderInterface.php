<?php
declare(strict_types=1);

namespace Xervice\DataProvider\Business\Model\DataProvider;


interface DataProviderInterface
{
    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @param array $data
     */
    public function fromArray(array $data): void;
}
