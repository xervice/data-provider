<?php


namespace Xervice\DataProvider\DataProvider;

/**
 * @method array getElements()
 */
abstract class AbstractDataProvider
{
    /**
     * @return array
     */
    public function toArray() : array
    {
        $data = $this->convertToArray($this);

        return $data;
    }

    /**
     * @param array $data
     */
    public function fromArray(array $data)
    {
        foreach ($this->getElements() as $element) {
            $fieldname = $element['name'];
            if (isset($data[$fieldname])) {
                if ($element['is_dataprovider']) {
                    $dataProvider = new $element['type']();
                    if (is_array($data[$fieldname])) {
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
     * @param \Xervice\DataProvider\DataProvider\AbstractDataProvider $provider
     *
     * @return array
     */
    private function convertToArray(AbstractDataProvider $provider) : array
    {
        $data = [];
        foreach ($provider->getElements() as $element) {
            $fieldname = $element['name'];
            $hasMethod = 'has' . $fieldname;
            if ($provider->$hasMethod()) {
                $getMethod = 'get' . $fieldname;
                if ($element['is_dataprovider'] && $provider->{$getMethod}() instanceof AbstractDataProvider) {
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