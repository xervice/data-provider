<?php

namespace XerviceTest\DataProvider;

use DataProvider\DefaultDataProvider;
use DataProvider\TestKeyValueCollectionDataProvider;
use DataProvider\TestKeyValueDataProvider;
use DataProvider\WildcardDataProvider;
use DataProvider\WithoutUnderlineConvertingDataProvider;
use DataProvider\WithUnderlineConvertingDataProvider;
use Xervice\Config\Business\XerviceConfig;
use Xervice\Core\Business\Model\Locator\Dynamic\Business\DynamicBusinessLocator;
use Xervice\DataProvider\DataProviderConfig;

/**
 * @method \Xervice\DataProvider\Business\DataProviderFacade getFacade()
 */
class IntegrationTest extends \Codeception\Test\Unit
{
    use DynamicBusinessLocator;

    /**
     * @var \XerviceTest\XerviceTester
     */
    protected $tester;

    protected function _before()
    {
        XerviceConfig::set(
            DataProviderConfig::DATA_PROVIDER_PATHS,
            [
                __DIR__ . '/Schema'
            ]
        );

        XerviceConfig::set(DataProviderConfig::FILE_PATTERN, '*.testprovider.xml');
        XerviceConfig::set(DataProviderConfig::DATA_PROVIDER_NAMESPACE, 'DataProvider');
        XerviceConfig::set(DataProviderConfig::DATA_PROVIDER_GENERATED_PATH, __DIR__. '/../../../src/Generated');

        $this->getFacade()->generateDataProvider();
    }


    /**
     * @group Xervice
     * @group DataProvider
     * @group Integration
     */
    public function testGeneration()
    {
        $this->getFacade()->cleanDataProvider();
        $this->assertEquals(
            [
                'DefaultDataProvider.php',
                'TestKeyValueDataProvider.php',
                'TestKeyValueCollectionDataProvider.php',
                'WildcardDataProvider.php',
                'WithoutUnderlineConvertingDataProvider.php',
                'WithUnderlineConvertingDataProvider.php'
            ],
            $this->getFacade()->generateDataProvider()
        );
    }

    /**
     * @group Xervice
     * @group DataProvider
     * @group Integration
     */
    public function testDataProviderDefautWithDefault()
    {
        $dataProvider = new DefaultDataProvider();

        $this->assertTrue($dataProvider->getBoolean());
        $this->assertEquals(
            'Text',
            $dataProvider->getString()
        );
        $this->assertEquals(
            intval(5),
            $dataProvider->getNumber()
        );
        $this->assertEquals(
            floatval(1.5),
            $dataProvider->getFloat()
        );
        $this->assertEquals(
            doubleval(2.5),
            $dataProvider->getDouble()
        );
        $this->assertEquals(
            [],
            $dataProvider->getList()
        );
        $this->assertEquals(
            '',
            $dataProvider->getEmptyText()
        );$this->assertNull(
            $dataProvider->getObject()
        );
    }

    /**
     * @group Xervice
     * @group DataProvider
     * @group Integration
     */
    public function textObject()
    {
        $object = new \stdClass();
        $object->test = 'bar';

        $defaultData = new DefaultDataProvider();
        $defaultData->setObject($object);

        $this->assertEquals(
            'bar',
            $defaultData->getObject()->test
        );
    }

    /**
     * @group Xervice
     * @group DataProvider
     * @group Integration
     */
    public function testDataProviderWithUnderlinesWithoutConverting()
    {
        $dataProvider = new WithoutUnderlineConvertingDataProvider();

        $this->assertTrue(
            method_exists($dataProvider, 'setIt_is_a_test')
        );
    }

    /**
     * @group Xervice
     * @group DataProvider
     * @group Integration
     */
    public function testDataProviderWithUnderlinesWithConverting()
    {
        $dataProvider = new WithUnderlineConvertingDataProvider();

        $this->assertTrue(
            method_exists($dataProvider, 'setItIsATest')
        );
    }

    /**
     * @group Xervice
     * @group DataProvider
     * @group Integration
     */
    public function testDataProviderWithNull()
    {
        $dataProvider = new TestKeyValueDataProvider();
        $dataProvider
            ->setKey('test')
            ->setValue(null)
            ->setDescription('Desc');

        $this->assertEquals(
            'test',
            $dataProvider->getKey()
        );

        $this->assertNull(
            $dataProvider->getValue()
        );

        $this->assertEquals(
            'Desc',
            $dataProvider->getDescription()
        );
    }

    /**
     * @group Xervice
     * @group DataProvider
     * @group Integration
     */
    public function testToAndFromArray()
    {
        $testData = [
            'Key' => 'test',
            'Value' => 'value'
        ];

        $keyValue = new TestKeyValueDataProvider();
        $keyValue->fromArray($testData);

        $list = new TestKeyValueCollectionDataProvider();
        $list->addOneValue($keyValue);

        $list->setChildValue(null);

        $resultData = $list->toArray();

        $list = new TestKeyValueCollectionDataProvider();
        $list->fromArray($resultData);

        $this->assertEquals(
            'test',
            $list->getKeyValues()[0]->getKey()
        );

        $this->assertEquals(
            'Test',
            $list->getKeyValues()[0]->getDefault()
        );

        $this->assertTrue(
            $list->getKeyValues()[0]->hasKey()
        );

        $list->getKeyValues()[0]->unsetKey();
        $dataTest = $list->getKeyValues()[0]->toArray();

        $this->assertArrayNotHasKey(
            'Key',
            $dataTest
        );
        $this->assertFalse(
            $list->getKeyValues()[0]->hasKey()
        );
    }

    /**
     * @group Xervice
     * @group DataProvider
     * @group Integration
     */
    public function testFromAndToArrayWithUnderlines()
    {
        $withUnderlines = new WithUnderlineConvertingDataProvider();
        $withUnderlines->setItIsATest('testToArray');

        $arrayData = $withUnderlines->toArray();

        $this->assertEquals(
            'testToArray',
            $arrayData['itIsATest']
        );

        $newDto = new WithUnderlineConvertingDataProvider();
        $newDto->fromArray($arrayData);

        $this->assertEquals(
            'testToArray',
            $newDto->getItIsATest()
        );

    }

    /**
     * @group Xervice
     * @group DataProvider
     * @group Integration
     */
    public function testWildcard()
    {
        $dataProvider = new TestKeyValueDataProvider();
        $dataProvider
            ->setKey('myKey')
            ->setValue('myVal')
            ->setDescription('myDesc');

        $wildcard = new WildcardDataProvider();
        $wildcard
            ->setOneDataProvider($dataProvider)
            ->addDataProvider($dataProvider)
            ->addDataProvider($dataProvider);

        $myArray = $wildcard->toArray();

        $this->assertEquals(
            TestKeyValueDataProvider::class,
            $myArray['OneDataProvider']['class']
        );

        $this->assertEquals(
            'myDesc',
            $myArray['OneDataProvider']['dataprovider']['Description']
        );

        $this->assertCount(
            2,
            $myArray['DataProviders']
        );

        $newWildcard = new WildcardDataProvider();
        $newWildcard->fromArray($myArray);

        $oneDataProvider = $newWildcard->getOneDataProvider();
        $this->assertWildcardProvider($oneDataProvider);

        foreach ($newWildcard->getDataProviders() as $dataProvider) {
            $this->assertWildcardProvider($dataProvider);
        }
    }

    public function testRightMethodeName()
    {
        $class = new \ReflectionClass(TestKeyValueDataProvider::class);

        $testKeyValueMethodNames = [];
        foreach ($class->getMethods() as $property) {
            $testKeyValueMethodNames[$property->getName()] = true;
        }

        $this->assertArrayHasKey('getKey', $testKeyValueMethodNames);
        $this->assertArrayHasKey('setKey', $testKeyValueMethodNames);
        $this->assertArrayHasKey('unsetKey', $testKeyValueMethodNames);
        $this->assertArrayHasKey('hasKey', $testKeyValueMethodNames);

        $this->assertArrayHasKey('getValue', $testKeyValueMethodNames);
        $this->assertArrayHasKey('setValue', $testKeyValueMethodNames);
        $this->assertArrayHasKey('unsetValue', $testKeyValueMethodNames);
        $this->assertArrayHasKey('hasValue', $testKeyValueMethodNames);

        $this->assertArrayHasKey('getIsActive', $testKeyValueMethodNames);
        $this->assertArrayHasKey('setIsActive', $testKeyValueMethodNames);
        $this->assertArrayHasKey('unsetIsActive', $testKeyValueMethodNames);
        $this->assertArrayHasKey('hasIsActive', $testKeyValueMethodNames);

        $this->assertArrayHasKey('getDescription', $testKeyValueMethodNames);
        $this->assertArrayHasKey('setDescription', $testKeyValueMethodNames);
        $this->assertArrayHasKey('unsetDescription', $testKeyValueMethodNames);
        $this->assertArrayHasKey('hasDescription', $testKeyValueMethodNames);

        $class = new \ReflectionClass(TestKeyValueCollectionDataProvider::class);

        $testKeyValueCollectionMethodNames = [];
        foreach ($class->getMethods() as $property) {
            $testKeyValueCollectionMethodNames[$property->getName()] = true;
        }

        $this->assertArrayHasKey('getKeyValues', $testKeyValueCollectionMethodNames);
        $this->assertArrayHasKey('setKeyValues', $testKeyValueCollectionMethodNames);
        $this->assertArrayHasKey('unsetKeyValues', $testKeyValueCollectionMethodNames);
        $this->assertArrayHasKey('hasKeyValues', $testKeyValueCollectionMethodNames);
    }

    public function testChangeDataProviderNamespace()
    {
        $generatedPath = __DIR__ . '/../../../src/Generated/DataTransferObject';
        if(!is_dir($generatedPath)) {
            mkdir($generatedPath);
        }
        $namespace = 'DataProvider\\DataTransferObject';
        XerviceConfig::set(DataProviderConfig::DATA_PROVIDER_NAMESPACE, $namespace);
        XerviceConfig::set(DataProviderConfig::DATA_PROVIDER_GENERATED_PATH,$generatedPath);
        $this->getFacade()->generateDataProvider();

        $this->assertTrue(class_exists(\DataProvider\DataTransferObject\TestKeyValueCollectionDataProvider::class));
        $this->assertTrue(class_exists(\DataProvider\DataTransferObject\TestKeyValueDataProvider::class));


        $testKeyValueCollection = [
            'keyValues' => [
                [
                    'Key' => 'Two'
                ],
                [
                    'Key' => 'Three'
                ]
            ],
            'ChildValue' => [
                'Key' => 'One'
            ]
        ];

        $testKeyValueCollectionDataProvider = new \DataProvider\DataTransferObject\TestKeyValueCollectionDataProvider();
        $testKeyValueCollectionDataProvider->fromArray($testKeyValueCollection);

        $this->assertInstanceOf(
            \DataProvider\DataTransferObject\TestKeyValueDataProvider::class,
            $testKeyValueCollectionDataProvider->getChildValue()
        );
        $this->assertSame('One', $testKeyValueCollectionDataProvider->getChildValue()->getKey());

        $this->assertInstanceOf(
            \DataProvider\DataTransferObject\TestKeyValueDataProvider::class,
            $testKeyValueCollectionDataProvider->getKeyValues()[0]
        );
        $this->assertInstanceOf(
            \DataProvider\DataTransferObject\TestKeyValueDataProvider::class,
            $testKeyValueCollectionDataProvider->getKeyValues()[1]
        );
        $this->assertSame('Two', $testKeyValueCollectionDataProvider->getKeyValues()[0]->getKey());
        $this->assertSame('Three', $testKeyValueCollectionDataProvider->getKeyValues()[1]->getKey());
    }

    /**
     * @param $oneDataProvider
     */
    private function assertWildcardProvider($oneDataProvider): void
    {
        $this->assertEquals(
            'myKey',
            $oneDataProvider->getKey()
        );
        $this->assertEquals(
            'myVal',
            $oneDataProvider->getValue()
        );
        $this->assertEquals(
            'myDesc',
            $oneDataProvider->getDescription()
        );
    }

}
