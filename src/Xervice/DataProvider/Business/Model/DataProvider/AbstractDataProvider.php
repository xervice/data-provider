<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Business\Model\DataProvider;

/**
 * @method array getElements()
 */
abstract class AbstractDataProvider implements DataProviderInterface
{
    /**
     * @return array
     */
    public function toArray() : array
    {
        return $this->convertToArray($this);
    }

    /**
     * @param array $data
     */
    public function fromArray(array $data): void
    {
        foreach ($this->getElements() as $element) {
            $fieldname = $element['name'];
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
    private function convertToArray(DataProviderInterface $provider) : array
    {
        $data = [];
        foreach ($provider->getElements() as $element) {
            $fieldname = $element['name'];
            $hasMethod = 'has' . $fieldname;
            if ($provider->$hasMethod()) {
                $data = $this->getDataFromFields($provider, $fieldname, $element, $data);
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param $element
     * @param $fieldname
     */
    private function setFieldFromArrayData(array $data, $element, $fieldname): void
    {
        if (strpos($element['type'], 'DataProviderInterface[]') !== false) {
            $this->setChildData($data, $fieldname);
        } elseif (strpos($element['type'], 'DataProviderInterface') !== false) {
            $this->setAnyDataProviderValues($data, $fieldname);
        } elseif ($element['is_dataprovider']) {
            $this->setOneDataProviderValue($data, $element, $fieldname);
        } elseif ($element['is_collection']) {
            $this->setCollectionValues($data, $element, $fieldname);
        }
        else {
            $this->{$fieldname} = $data[$fieldname];
        }
    }

    /**
     * @param \Xervice\DataProvider\Business\Model\DataProvider\DataProviderInterface $provider
     * @param $fieldname
     * @param $element
     * @param $data
     *
     * @return mixed
     */
    private function getDataFromFields(DataProviderInterface $provider, $fieldname, $element, $data)
    {
        $getMethod = 'get' . $fieldname;
        if (strpos($element['type'], 'DataProviderInterface[]') !== false) {
            $data = $this->getDataProviderCollectionData($provider, $fieldname, $data, $getMethod);
        } elseif (
            strpos($element['type'], 'DataProviderInterface') !== false
            && $provider->{$getMethod}() instanceof DataProviderInterface
        ) {
            $anyDataProvider = new AnyDataProvider($provider->{$getMethod}());
            $data[$fieldname] = $anyDataProvider->toArray();
        } elseif ($element['is_dataprovider'] && $provider->{$getMethod}() instanceof DataProviderInterface) {
            $data[$fieldname] = $this->convertToArray($provider->{$getMethod}());
        } elseif ($element['is_collection']) {
            $data = $this->getCollectionData($provider, $fieldname, $data, $getMethod);
        } else {
            $data[$fieldname] = $provider->{$getMethod}();
        }
        return $data;
}

    /**
     * @param array $data
     * @param $fieldname
     */
    private function setChildData(array $data, $fieldname): void
    {
        foreach ($data[$fieldname] as $childData) {
            $anyDataProvider = new AnyDataProvider();
            $anyDataProvider->fromArray($childData);
            $this->{$fieldname}[] = $anyDataProvider->getDataProvider();
        }
    }

    /**
     * @param array $data
     * @param $fieldname
     */
    private function setAnyDataProviderValues(array $data, $fieldname): void
    {
        $anyDataProvider = new AnyDataProvider();
        $anyDataProvider->fromArray($data[$fieldname]);
        $this->{$fieldname} = $anyDataProvider->getDataProvider();
    }

    /**
     * @param array $data
     * @param $element
     * @param $fieldname
     */
    private function setOneDataProviderValue(array $data, $element, $fieldname): void
    {
        $dataProvider = new $element['type']();
        if (\is_array($data[$fieldname])) {
            $dataProvider->fromArray($data[$fieldname]);
        }
        $this->{$fieldname} = $dataProvider;
    }

    /**
     * @param array $data
     * @param $element
     * @param $fieldname
     */
    private function setCollectionValues(array $data, $element, $fieldname): void
    {
        foreach ($data[$fieldname] as $childData) {
            $dataProvider = new $element['singleton_type']();
            $dataProvider->fromArray($childData);
            $this->{$fieldname}[] = $dataProvider;
        }
    }

    /**
     * @param \Xervice\DataProvider\Business\Model\DataProvider\DataProviderInterface $provider
     * @param $fieldname
     * @param $data
     * @param $getMethod
     *
     * @return mixed
     */
    private function getDataProviderCollectionData(DataProviderInterface $provider, $fieldname, $data, $getMethod)
    {
        $data[$fieldname] = [];
        foreach ($provider->{$getMethod}() as $child) {
            $anyDataProvider = new AnyDataProvider($child);
            $data[$fieldname][] = $anyDataProvider->toArray();
        }
        return $data;
}

    /**
     * @param \Xervice\DataProvider\Business\Model\DataProvider\DataProviderInterface $provider
     * @param $fieldname
     * @param $data
     * @param $getMethod
     *
     * @return mixed
     */
    private function getCollectionData(DataProviderInterface $provider, $fieldname, $data, $getMethod)
    {
        $data[$fieldname] = [];
        foreach ($provider->{$getMethod}() as $child) {
            $data[$fieldname][] = $child->toArray();
        }
        return $data;
}
}