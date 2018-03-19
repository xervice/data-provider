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
                    $dataProvider->fromArray($data[$fieldname]);
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
            if ($element['is_dataprovider']) {
                $data[$element['name']] = $this->convertToArray($this->{$fieldname});
            } elseif ($element['is_collection']) {
                $data[$element['name']] = [];
                foreach ($this->{$fieldname} as $child) {
                    $data[$element['name']][] = $child->toArray();
                }
            } else {
                $data[$element['name']] = $this->{$fieldname};
            }
        }

        return $data;
    }
}