<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Business\Model\Parser;

use Xervice\DataProvider\Business\Model\DataProvider\DataProviderInterface;

class XmlMerger implements XmlMergerInterface
{
    /**
     * @var array
     */
    private $mergedXml = [];

    /**
     * @var string
     */
    private $namespace;

    /**
     * @param string $namespace
     */
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param string $xmlContent
     */
    public function addXml(string $xmlContent): void
    {
        $xml = \simplexml_load_string($xmlContent);

        foreach ($xml->DataProvider as $xmlDataProvider) {
            $dataProvider = $this->parseDataProvider($xmlDataProvider);

            foreach ($xmlDataProvider->DataElement as $element) {
                $fieldName = (string)$element->attributes()['name'];

                if (isset($this->mergedXml[$dataProvider]['elements'][$fieldName])) {
                    $this->mergedXml[$dataProvider]['elements'][$fieldName] = array_merge(
                        $this->mergedXml[$dataProvider]['elements'][$fieldName],
                        $this->getElementData($element, $this->mergedXml[$dataProvider])
                    );
                }
                else {
                    $this->mergedXml[$dataProvider]['elements'][$fieldName] = $this->getElementData($element, $this->mergedXml[$dataProvider]);
                }
            }
        }


    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->mergedXml;
    }

    /**
     * @param \SimpleXMLElement $xmlDataProvider
     *
     * @return string
     */
    private function parseDataProvider(\SimpleXMLElement $xmlDataProvider): string
    {
        $dataProvider = (string)$xmlDataProvider->attributes()['name'];
        if (!isset($this->mergedXml[$dataProvider])) {
            $this->mergedXml[$dataProvider] = [
                'configs' => [
                    'convertUnderlines' => (bool)$xmlDataProvider->attributes()['ConvertUnderlines'] ?? false,
                    'deprecated' => (bool)$xmlDataProvider->attributes()['deprecated'] ?? false
                ],
                'elements' => []
            ];
        }

        return $dataProvider;
    }

    /**
     * @param \SimpleXMLElement $element
     *
     * @return array
     */
    private function getElementData(\SimpleXMLElement $element, array $dataProvider): array
    {
        $type = (string)$element->attributes()['type'];

        $type = ($type !== 'double') ? $type : 'float';

        $allowNull = (bool)$element->attributes()['allownull'];
        $default = (string)$element->attributes()['default'];

        if ($type === 'object') {
            $allowNull = true;
            $default = null;
        }

        $data = [
            'name' => (string)$element->attributes()['name'],
            'allownull' => $allowNull,
            'default' => $default,
            'type' => $this->getVariableType($type),
            'is_collection' => $this->isCollection($type),
            'is_dataprovider' => $this->isDataProvider($type),
            'isCamelCase' => $dataProvider['configs']['convertUnderlines']
        ];

        $singleton = (string)$element->attributes()['singleton'];
        if ($singleton !== '') {
            $data['singleton'] = (string)$element->attributes()['singleton'];
            $data['singleton_type'] = $this->getSingleVariableType($type);
        }

        return $data;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isDataProvider(string $type): bool
    {
        return (!$this->isSimpleType($type) && !$this->isCollection($type));
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isCollection(string $type): bool
    {
        return strpos($type, '[]') !== false;
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
            'float',
            'array',
            'object',
            'DataProviderInterface',
            'DataProviderInterface[]',
            DataProviderInterface::class,
            DataProviderInterface::class . '[]'
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
            $namespace = ltrim($this->namespace, '\\');
            $type =  '\\' . $namespace .'\\' . $type . 'DataProvider';
        }

        if ($type === 'DataProviderInterface') {
            $type = '\\' . DataProviderInterface::class;
        }

        if ($type === 'DataProviderInterface[]') {
            $type = '\\' . DataProviderInterface::class . '[]';
        }

        if (strpos($type, '[]') !== false) {
            $type = str_replace('[]', '', $type) . '[]';
        }

        return $type;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getSingleVariableType(string $type): string
    {
        if (!$this->isSimpleType($type)) {
            $namespace = ltrim($this->namespace, '\\');
            $type = '\\' . $namespace .'\\' . $type . 'DataProvider';
        }

        if ($type === 'DataProviderInterface') {
            $type = '\\' . DataProviderInterface::class;
        }

        if ($type === 'DataProviderInterface[]') {
            $type = '\\' . DataProviderInterface::class . '[]';
        }

        if (strpos($type, '[]') !== false) {
            $type = str_replace('[]', '', $type);
        }

        return $type;
    }
}
