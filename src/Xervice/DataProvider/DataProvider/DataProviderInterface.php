<?php

namespace Xervice\DataProvider\DataProvider;


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