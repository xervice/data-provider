<?php


namespace Xervice\DataProvider\Parser;

use Xervice\DataProvider\DataProvider\AbstractDataProvider;

class XmlMerger implements XmlMergerInterface
{
    /**
     * @var array
     */
    private $mergedXml = [];

    /**
     * @param string $xml
     */
    public function addXml(string $xml)
    {
        $xml = simplexml_load_string($xml);

        foreach ($xml->DataProvider as $xmlDataProvider) {
            $dataProvider = $this->parseDataProvider($xmlDataProvider);

            foreach ($xmlDataProvider->DataElement as $element) {
                $fieldName = (string)$element->attributes()['name'];

                if (!isset($this->mergedXml[$dataProvider][$fieldName])) {
                    $this->mergedXml[$dataProvider][$fieldName] = $this->getElementData($element);
                }
                else {
                    $this->mergedXml[$dataProvider][$fieldName] = array_merge(
                        $this->mergedXml[$dataProvider][$fieldName],
                        $this->getElementData($element)
                    );
                }
            }
        }


    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->mergedXml;
    }

    /**
     * @param \SimpleXMLElement $xmlDataProvider
     *
     * @return string
     */
    private function parseDataProvider(\SimpleXMLElement $xmlDataProvider)
    {
        $dataProvider = (string)$xmlDataProvider->attributes()['name'];
        if (!isset($this->mergedXml[$dataProvider])) {
            $this->mergedXml[$dataProvider] = [];
        }

        return $dataProvider;
    }

    /**
     * @param $element
     *
     * @return array
     */
    private function getElementData(\SimpleXMLElement $element): array
    {
        $type = (string)$element->attributes()['type'];

        $data = [
            'name'      => (string)$element->attributes()['name'],
            'type'      => $this->getVariableType($type),
            'is_collection'  => $this->isCollection($type),
            'is_dataprovider' => $this->isDataProvider($type)
        ];

        $singleton = (string)$element->attributes()['singleton'];
        if ($singleton !== '') {
            $data['singleton'] = (string)$element->attributes()['singleton'];
            $data['singleton_type'] = $this->getVariableType((string)$element->attributes()['singleton']);
        }

        return $data;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isDataProvider(string $type) : bool
    {
        return (!$this->isSimpleType($type) && !$this->isCollection($type));
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isCollection(string $type) : bool
    {
        if (!$this->isSimpleType($type)) {
            if (strpos($type, '[]') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isSimpleType(string $type): bool
    {
        $validTypes = [
            'int',
            'string',
            'bool',
            'double',
            'array'
        ];

        return \in_array($type, $validTypes, true);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getVariableType(string $type): string
    {
        if (!$this->isSimpleType($type)) {
            $type = $this->getDataProviderType($type);
        }

        return $type;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getDataProviderType(string $type): string
    {
        if ($type === 'AbstractDataProvider') {
            $type = '\\' . AbstractDataProvider::class;
        } elseif ($type === 'AbstractDataProvider[]') {
            $type = '\\' . AbstractDataProvider::class . '[]';
        } else {
            $type = '\DataProvider\\' . $type . 'DataProvider';
        }
        if (strpos($type, '[]') !== false) {
            $type = str_replace('[]', '', $type) . '[]';
        }
        return $type;
    }
}