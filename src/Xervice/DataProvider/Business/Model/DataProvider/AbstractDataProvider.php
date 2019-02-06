<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Business\Model\DataProvider;

abstract class AbstractDataProvider implements DataProviderInterface
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->convertToArray($this);
    }

    /**
     * @param array $data
     */
    public function fromArray(array $data): void
    {
        foreach ($this->getElements() as $element) {
            $fieldname = $element['isCamelCase'] ? $this->convertUnderlines($element['name']) : $element['name'];
            if (isset($data[$fieldname])) {
                $this->setFieldFromArrayData($data, $element, $fieldname);
            }
        }
    }

    /**
     * @param \Xervice\DataProvider\Business\Model\DataProvider\DataProviderInterface $provider
     *
     * @return array
     */
    private function convertToArray(DataProviderInterface $provider): array
    {
        $data = [];
        foreach ($provider->getElements() as $element) {
            $fieldname = $element['isCamelCase'] ? $this->convertUnderlines($element['name']) : $element['name'];
            $hasMethod = 'has' . $fieldname;
            if ($provider->$hasMethod()) {
                $data = $this->getDataFromFields($provider, $fieldname, $element, $data);
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param array $element
     * @param string $fieldname
     */
    private function setFieldFromArrayData(array $data, array $element, string $fieldname): void
    {
        if (strpos($element['type'], 'DataProviderInterface[]') !== false) {
            $this->setChildData($data, $element, $fieldname);
        }
        elseif (strpos($element['type'], 'DataProviderInterface') !== false) {
            $this->setAnyDataProviderValues($data, $element, $fieldname);
        }
        elseif ($element['is_dataprovider']) {
            $this->setOneDataProviderValue($data, $element, $fieldname);
        }
        elseif ($element['is_collection']) {
            $this->setCollectionValues($data, $element, $fieldname);
        }
        else {
            $this->{$element['name']} = $data[$fieldname];
        }
    }

    /**
     * @param \Xervice\DataProvider\Business\Model\DataProvider\DataProviderInterface $provider
     * @param string $fieldname
     * @param array $element
     * @param array $data
     *
     * @return mixed
     */
    private function getDataFromFields(DataProviderInterface $provider, string $fieldname, array $element, array $data)
    {
        $getMethod = 'get' . $fieldname;
        if (strpos($element['type'], 'DataProviderInterface[]') !== false) {
            $data = $this->getDataProviderCollectionData($provider, $fieldname, $data, $getMethod);
        }
        elseif (
            strpos($element['type'], 'DataProviderInterface') !== false
            && $provider->{$getMethod}() instanceof DataProviderInterface
        ) {
            $anyDataProvider = new AnyDataProvider($provider->{$getMethod}());
            $data[$fieldname] = $anyDataProvider->toArray();
        }
        elseif ($element['is_dataprovider'] && $provider->{$getMethod}() instanceof DataProviderInterface) {
            $data[$fieldname] = $this->convertToArray($provider->{$getMethod}());
        }
        elseif ($element['is_collection']) {
            $data = $this->getCollectionData($provider, $fieldname, $data, $getMethod);
        }
        else {
            $data[$fieldname] = $provider->{$getMethod}();
        }
        return $data;
    }

    /**
     * @param array $data
     * @param array $element
     * @param string $fieldname
     */
    private function setChildData(array $data, array $element, string $fieldname): void
    {
        foreach ($data[$fieldname] as $childData) {
            $anyDataProvider = new AnyDataProvider();
            $anyDataProvider->fromArray($childData);
            $this->{$element['name']}[] = $anyDataProvider->getDataProvider();
        }
    }

    /**
     * @param array $data
     * @param array $element
     * @param string $fieldname
     */
    private function setAnyDataProviderValues(array $data, array $element, string $fieldname): void
    {
        $anyDataProvider = new AnyDataProvider();
        $anyDataProvider->fromArray($data[$fieldname]);
        $this->{$element['name']} = $anyDataProvider->getDataProvider();
    }

    /**
     * @param array $data
     * @param array $element
     * @param string $fieldname
     */
    private function setOneDataProviderValue(array $data, array $element, string $fieldname): void
    {
        $dataProvider = new $element['type']();
        if (\is_array($data[$fieldname])) {
            $dataProvider->fromArray($data[$fieldname]);
        }
        $this->{$element['name']} = $dataProvider;
    }

    /**
     * @param array $data
     * @param array $element
     * @param string $fieldname
     */
    private function setCollectionValues(array $data, array $element, string $fieldname): void
    {
        foreach ($data[$fieldname] as $childData) {
            $dataProvider = new $element['singleton_type']();
            $dataProvider->fromArray($childData);
            $this->{$element['name']}[] = $dataProvider;
        }
    }

    /**
     * @param \Xervice\DataProvider\Business\Model\DataProvider\DataProviderInterface $provider
     * @param string $fieldname
     * @param array $data
     * @param string $getMethod
     *
     * @return array
     */
    private function getDataProviderCollectionData(
        DataProviderInterface $provider,
        string $fieldname,
        array $data,
        string $getMethod
    ): array {
        $data[$fieldname] = [];
        foreach ($provider->{$getMethod}() as $child) {
            $anyDataProvider = new AnyDataProvider($child);
            $data[$fieldname][] = $anyDataProvider->toArray();
        }
        return $data;
    }

    /**
     * @param \Xervice\DataProvider\Business\Model\DataProvider\DataProviderInterface $provider
     * @param string $fieldname
     * @param array $data
     * @param string $getMethod
     *
     * @return array
     */
    private function getCollectionData(
        DataProviderInterface $provider,
        string $fieldname,
        array $data,
        string $getMethod
    ): array {
        $data[$fieldname] = [];
        foreach ($provider->{$getMethod}() as $child) {
            $data[$fieldname][] = $child->toArray();
        }
        return $data;
    }

    /**
     * @param string $methodName
     *
     * @return string
     */
    private function convertUnderlines(string $methodName): string
    {
        return preg_replace_callback(
            '@\_([a-z]{1,1})@',
            function ($matches) {
                return strtoupper($matches[1] ?? '');
            },
            $methodName
        );
    }

    /**
     * @return array
     */
    abstract protected function getElements(): array;
}