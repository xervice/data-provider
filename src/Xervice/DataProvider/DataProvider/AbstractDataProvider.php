<?php
declare(strict_types=1);


namespace Xervice\DataProvider\DataProvider;

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
                if ($element['is_dataprovider']) {
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
     * @param \Xervice\DataProvider\DataProvider\DataProviderInterface $provider
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
                if ($element['is_dataprovider'] && $provider->{$getMethod}() instanceof DataProviderInterface) {
                    $data[$fieldname] = $this->convertToArray($provider->{$getMethod}());
                }
                elseif ($element['is_collection']) {
                    $data[$fieldname] = [];
                    foreach ($provider->{$getMethod}() as $child) {
                        $data[$fieldname][] = $child->toArray();
                    }
                }
                else {
                    $data[$fieldname] = $provider->{$getMethod}();
                }
            }
        }

        return $data;
    }
}