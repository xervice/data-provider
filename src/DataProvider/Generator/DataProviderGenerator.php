<?php


namespace Xervice\DataProvider\Generator;


use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Xervice\DataProvider\DataProvider\AbstractDataProvider;
use Xervice\DataProvider\Parser\DataProviderParserInterface;

class DataProviderGenerator implements DataProviderGeneratorInterface
{
    /**
     * @var \Xervice\DataProvider\Parser\DataProviderParserInterface
     */
    private $parser;

    /**
     * @var \Xervice\DataProvider\Generator\FileWriterInterface
     */
    private $fileWriter;

    /**
     * DataProviderGenerator constructor.
     *
     * @param \Xervice\DataProvider\Parser\DataProviderParserInterface $parser
     * @param \Xervice\DataProvider\Generator\FileWriterInterface $fileWriter
     */
    public function __construct(
        DataProviderParserInterface $parser,
        FileWriterInterface $fileWriter
    ) {
        $this->parser = $parser;
        $this->fileWriter = $fileWriter;
    }

    /**
     * @return array
     */
    public function generate(): array
    {
        $fileGenerated = [];

        foreach ($this->parser->getDataProvider() as $providerName => $providerElements) {
            $namespace = new PhpNamespace('DataProvider');
            $dataProvider = $this->createDataProviderClass($providerName, $providerElements, $namespace);
            $this->fileWriter->writeToFile($dataProvider->getName() . '.php', (string)$namespace);
            $fileGenerated[] = $dataProvider->getName() . '.php';
        }

        return $fileGenerated;
    }

    /**
     * @param string $provider
     * @param \Nette\PhpGenerator\PhpNamespace $namespace
     *
     * @return \Nette\PhpGenerator\ClassType
     */
    private function createNewDataProvider($provider, PhpNamespace $namespace): ClassType
    {
        $dataProvider = $namespace->addClass($provider . 'DataProvider');
        $dataProvider
            ->setFinal()
            ->setExtends(AbstractDataProvider::class)
            ->setComment('Auto generated data provider');

        return $dataProvider;
    }

    /**
     * @param $dataProvider
     * @param $element
     */
    private function addGetter(ClassType $dataProvider, $element): void
    {
        $dataProvider->addMethod('get' . $element['name'])
                     ->addComment('@return ' . $element['type'])
                     ->setVisibility('public')
                     ->setBody('return $this->' . $element['name'] . ';')
                     ->setReturnType($this->getTypeHint($element['type']));
    }

    /**
     * @param $dataProvider
     * @param $element
     */
    private function addSetter(ClassType $dataProvider, $element): void
    {
        $setter = $dataProvider->addMethod('set' . $element['name'])
                               ->addComment(
                                   '@param ' . $element['type'] . ' $'
                                   . $element['name']
                               )
                               ->addComment('@return ' . $dataProvider->getName())
                               ->setVisibility('public')
                               ->setBody(
                                   '$this->' . $element['name'] . ' = $' . $element['name'] . ';' . PHP_EOL . PHP_EOL
                                   . 'return $this;'
                               );

        $setter->addParameter($element['name'])
               ->setTypeHint($this->getTypeHint($element['type']));
    }

    /**
     * @param $element
     * @param $dataProvider
     */
    private function addSingleSetter($element, ClassType $dataProvider)
    {
        if (isset($element['singleton']) && $element['singleton'] !== '') {
            $singleSetter = $dataProvider->addMethod('add' . $element['singleton'])
                                         ->addComment(
                                             '@param ' . $element['singleton'] . ' $'
                                             . $element['singleton']
                                         )
                                         ->setVisibility('public')
                                         ->setBody(
                                             '$this->' . $element['name'] . '[] = $' . $element['singleton'] . ';'
                                         );

            $singleSetter->addParameter($element['singleton'])
                         ->setTypeHint($element['singleton_type']);
        }
    }

    /**
     * @param $dataProvider
     * @param $element
     */
    private function addProperty(ClassType $dataProvider, $element): void
    {
        $dataProvider->addProperty($element['name'])
                     ->setVisibility('protected')
                     ->addComment('@var ' . $element['type']);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getTypeHint(string $type): string
    {
        if (strpos($type, '[]') !== false) {
            $type = 'array';
        }

        return $type;
    }

    /**
     * @param $dataProvider
     * @param $elements
     */
    private function addElementsGetter($dataProvider, $elements): void
    {
        $dataProvider->addMethod('getElements')
                     ->setReturnType('array')
                     ->setVisibility('protected')
                     ->addComment('@return array')
                     ->setBody('return ' . var_export($elements, true) . ';');
    }

    /**
     * @param $providerName
     * @param $providerElements
     * @param $namespace
     *
     * @return \Nette\PhpGenerator\ClassType
     */
    private function createDataProviderClass($providerName, $providerElements, $namespace
    ): \Nette\PhpGenerator\ClassType {
        $dataProvider = $this->createNewDataProvider($providerName, $namespace);

        foreach ($providerElements as $element) {
            $this->addProperty($dataProvider, $element);
            $this->addGetter($dataProvider, $element);
            $this->addSetter($dataProvider, $element);
            $this->addSingleSetter($element, $dataProvider);
        }

        $this->addElementsGetter($dataProvider, $providerElements);
        return $dataProvider;
    }
}