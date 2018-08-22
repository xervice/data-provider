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
                if (strpos($element['type'], 'DataProviderInterface[]') !== false) {
                    foreach ($data[$fieldname] as $childData) {
                        $anyDataProvider = new AnyDataProvider();
                        $anyDataProvider->fromArray($childData);
                        $this->{$fieldname}[] = $anyDataProvider->getDataProvider();
                    }
                } elseif (strpos($element['type'], 'DataProviderInterface') !== false) {
                    $anyDataProvider = new AnyDataProvider();
                    $anyDataProvider->fromArray($data[$fieldname]);
                    $this->{$fieldname} = $anyDataProvider->getDataProvider();
                } elseif ($element['is_dataprovider']) {
                    $dataProvider = new $element['type']();
                    if (\is_array($data[$fieldname])) {
                        $dataProvider->fromArray($data[$fieldname]);
                    }
                    $this->{$fieldname} = $dataProvider;
                } elseif ($element['is_collection']) {
                    foreach ($data[$fieldname] as $childData) {
                        $dataProvider = new $element['singleton_type']();
                        $dataProvider->fromArray($childData);
                        $this->{$fieldname}[] = $dataProvider;
                    }
                } else {
                    $this->{$fieldname} = $data[$fieldname];
                }
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
                $getMethod = 'get' . $fieldname;
                if (strpos($element['type'], 'DataProviderInterface[]') !== false) {
                    $data[$fieldname] = [];
                    foreach ($provider->{$getMethod}() as $child) {
                        $anyDataProvider = new AnyDataProvider($child);
                        $data[$fieldname][] = $anyDataProvider->toArray();
                    }
                } elseif (
                    strpos($element['type'], 'DataProviderInterface') !== false
                    && $provider->{$getMethod}() instanceof DataProviderInterface
                ) {
                    $anyDataProvider = new AnyDataProvider($provider->{$getMethod}());
                    $data[$fieldname] = $anyDataProvider->toArray();
                } elseif ($element['is_dataprovider'] && $provider->{$getMethod}() instanceof DataProviderInterface) {
                    $data[$fieldname] = $this->convertToArray($provider->{$getMethod}());
                } elseif ($element['is_collection']) {
                    $data[$fieldname] = [];
                    foreach ($provider->{$getMethod}() as $child) {
                        $data[$fieldname][] = $child->toArray();
                    }
                } else {
                    $data[$fieldname] = $provider->{$getMethod}();
                }
            }
        }

        return $data;
    }
}