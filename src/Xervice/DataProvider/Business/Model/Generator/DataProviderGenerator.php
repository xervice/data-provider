<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Business\Model\Generator;


use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpNamespace;
use Xervice\DataProvider\Business\Model\DataProvider\DataProviderInterface;
use Xervice\DataProvider\Business\Model\Parser\DataProviderParserInterface;

class DataProviderGenerator implements DataProviderGeneratorInterface
{
    /**
     * @var \Xervice\DataProvider\Business\Model\Parser\DataProviderParserInterface
     */
    private $parser;

    /**
     * @var FileWriterInterface
     */
    private $fileWriter;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $dataProviderExtends;

    /**
     * DataProviderGenerator constructor.
     *
     * @param \Xervice\DataProvider\Business\Model\Parser\DataProviderParserInterface $parser
     * @param FileWriterInterface $fileWriter
     * @param string $namespace
     */
    public function __construct(
        DataProviderParserInterface $parser,
        FileWriterInterface $fileWriter,
        string $namespace,
        string $dataProviderExtends
    ) {
        $this->parser = $parser;
        $this->fileWriter = $fileWriter;
        $this->namespace = $namespace;
        $this->dataProviderExtends = $dataProviderExtends;
    }

    /**
     * @return array
     * @throws \Nette\InvalidArgumentException
     */
    public function generate(): array
    {
        $fileGenerated = [];

        foreach ($this->parser->getDataProvider() as $providerName => $providerData) {
            $namespace = new PhpNamespace($this->namespace);
            $dataProvider = $this->createDataProviderClass($providerName, $providerData, $namespace);
            $classContent = (string)$namespace;
            $classContent = str_replace('\?', '?', $classContent);
            $classContent = Helpers::tabsToSpaces($classContent, 4);
            $this->fileWriter->writeToFile($dataProvider->getName() . '.php', $classContent);
            $fileGenerated[] = $dataProvider->getName() . '.php';
        }

        return $fileGenerated;
    }

    /**
     * @param string $provider
     * @param \Nette\PhpGenerator\PhpNamespace $namespace
     *
     * @return ClassType
     * @throws \Nette\InvalidArgumentException
     */
    private function createNewDataProvider($provider, PhpNamespace $namespace): ClassType
    {
        $dataProvider = $namespace->addClass($provider . 'DataProvider');
        $dataProvider
            ->setFinal()
            ->setExtends($this->dataProviderExtends)
            ->setImplements(
                [
                    DataProviderInterface::class
                ]
            )
            ->setComment('Auto generated data provider')
        ;

        return $dataProvider;
    }

    /**
     * @param \Nette\PhpGenerator\ClassType $dataProvider
     * @param array $element
     * @param array $configs
     */
    private function addGetter(ClassType $dataProvider, array $element, array $configs): void
    {
        $dataProvider->addMethod('get' . $this->formatElementName($element['name'], $configs))
                     ->addComment('@return ' . $element['type'])
                     ->setVisibility('public')
                     ->setBody('return $this->' . $element['name'] . ';')
                     ->setReturnType($this->getTypeHint($element['type'], $element['allownull']))
        ;
    }

    /**
     * @param \Nette\PhpGenerator\ClassType $dataProvider
     * @param array $element
     * @param array $configs
     */
    private function addUnsetter(ClassType $dataProvider, array $element, array $configs): void
    {
        $dataProvider->addMethod('unset' . $this->formatElementName($element['name'], $configs))
                     ->addComment('@return ' . $dataProvider->getName())
                     ->setVisibility('public')
                     ->setBody('$this->' . $element['name'] . ' = null;' . PHP_EOL . PHP_EOL . 'return $this;')
        ;
    }

    /**
     * @param \Nette\PhpGenerator\ClassType $dataProvider
     * @param array $element
     * @param array $configs
     */
    private function addHas(ClassType $dataProvider, array $element, array $configs): void
    {
        $dataProvider->addMethod('has' . $this->formatElementName($element['name'], $configs))
                     ->addComment('@return bool')
                     ->setVisibility('public')
                     ->setBody(
                         'return ($this->' . $element['name'] . ' !== null && $this->' . $element['name'] . ' !== []);'
                     )
        ;
    }

    /**
     * @param \Nette\PhpGenerator\ClassType $dataProvider
     * @param array $element
     * @param array $configs
     */
    private function addSetter(ClassType $dataProvider, array $element, array $configs): void
    {
        $setter = $dataProvider->addMethod('set' . $this->formatElementName($element['name'], $configs))
                               ->addComment(
                                   '@param ' . $element['type'] . ' $'
                                   . $element['name']
                               )
                               ->addComment('@return ' . $dataProvider->getName())
                               ->setVisibility('public')
                               ->setBody(
                                   '$this->' . $element['name'] . ' = $' . $element['name'] . ';' . PHP_EOL . PHP_EOL
                                   . 'return $this;'
                               )
        ;

        $param = $setter->addParameter($element['name'])
                        ->setTypeHint($this->getTypeHint($element['type'], $element['allownull']))
        ;
        if ($element['default']) {
            $default = $this->getDefaultValue($element);
            $param->setDefaultValue($default);
        }
        elseif ($element['allownull']) {
            $param->setDefaultValue(null);
        }
    }

    /**
     * @param array $element
     * @param \Nette\PhpGenerator\ClassType $dataProvider
     * @param array $configs
     */
    private function addSingleSetter(array $element, ClassType $dataProvider, array $configs): void
    {
        if (isset($element['singleton']) && $element['singleton'] !== '') {
            $methodName = $configs['convertUnderlines']
                ? $this->convertUnderlines($element['singleton'])
                : $element['singleton'];

            $singleSetter = $dataProvider
                ->addMethod('add' . $methodName)
                ->addComment(
                    '@param ' . $element['singleton_type'] . ' $'
                    . $element['singleton']
                )
                ->addComment('@return ' . $dataProvider->getName())
                ->setVisibility('public')
                ->setBody(
                    sprintf(
                        '$this->%s[] = $%s; return $this;',
                        $element['name'],
                        $element['singleton']
                    )
                )
            ;

            $singleSetter->addParameter($element['singleton'])
                         ->setTypeHint($element['singleton_type'])
            ;
        }
    }

    /**
     * @param \Nette\PhpGenerator\ClassType $dataProvider
     * @param array $element
     */
    private function addProperty(ClassType $dataProvider, array $element): void
    {
        $property = $dataProvider->addProperty($element['name'])
                                 ->setVisibility('protected')
                                 ->addComment('@var ' . $element['type'])
        ;

        if ($element['default']) {
            $default = $this->getDefaultValue($element);
            $property->setValue($default);
        }
        elseif (strpos($element['type'], '[]') !== false) {
            $property->setValue([]);
        }
    }

    /**
     * @param string $type
     * @param bool $allowNull
     *
     * @return string
     */
    private function getTypeHint(string $type, bool $allowNull = null): string
    {
        if (strpos($type, '[]') !== false) {
            $type = 'array';
        }

        if ($allowNull === true) {
            $type = '?' . $type;
        }

        return $type;
    }

    /**
     * @param \Nette\PhpGenerator\ClassType $dataProvider
     * @param array $elements
     */
    private function addElementsGetter(ClassType $dataProvider, array $elements): void
    {
        $dataProvider->addMethod('getElements')
                     ->setReturnType('array')
                     ->setVisibility('protected')
                     ->addComment('@return array')
                     ->setBody('return ' . var_export($elements, true) . ';')
        ;
    }

    /**
     * @param string $providerName
     * @param array $providerData
     * @param \Nette\PhpGenerator\PhpNamespace $namespace
     *
     * @return \Nette\PhpGenerator\ClassType
     */
    private function createDataProviderClass(
        string $providerName,
        array $providerData,
        PhpNamespace $namespace
    ): ClassType {
        $dataProvider = $this->createNewDataProvider($providerName, $namespace);
        $providerElements = $providerData['elements'];

        foreach ($providerElements as $element) {
            $this->addProperty($dataProvider, $element);
            $this->addGetter($dataProvider, $element, $providerData['configs']);
            $this->addSetter($dataProvider, $element, $providerData['configs']);
            $this->addUnsetter($dataProvider, $element, $providerData['configs']);
            $this->addHas($dataProvider, $element, $providerData['configs']);
            $this->addSingleSetter($element, $dataProvider, $providerData['configs']);

        }

        $this->addElementsGetter($dataProvider, $providerElements);
        return $dataProvider;
    }

    /**
     * @param array $element
     *
     * @return bool
     */
    private function getDefaultValue($element)
    {
        $default = $element['default'];

        switch ($element['type']) {
            case 'bool':
            case 'boolean':
                {
                    $default = $default === 'false' ? false : $default;
                    $default = $default === 'true' ? true : $default;
                }
                break;

            case 'array':
                {
                    $default = [];
                }
                break;

            case 'string':
                {
                    $default = $default === '\'\'' ? '' : $default;
                }
                break;
        }

        settype($default, $element['type']);
        return $default;
    }

    /**
     * @param string $elementName
     *
     * @return string
     */
    private function formatElementName(string $elementName, array $configs): string
    {
        $elementName = ucfirst($elementName);

        if ($configs['convertUnderlines']) {
            $elementName = $this->convertUnderlines($elementName);
        }

        return $elementName;
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
}
