<?php
namespace XerviceTest\DataProvider;

use DataProvider\DefaultDataProvider;
use DataProvider\TestKeyValueCollectionDataProvider;
use DataProvider\TestKeyValueDataProvider;
use DataProvider\WildcardDataProvider;
use Xervice\Config\XerviceConfig;
use Xervice\Core\Locator\Dynamic\DynamicLocator;
use Xervice\DataProvider\DataProviderConfig;

/**
 * @method \Xervice\DataProvider\DataProviderFacade getFacade()
 */
class IntegrationTest extends \Codeception\Test\Unit
{
    use DynamicLocator;

    /**
     * @var \XerviceTest\XerviceTester
     */
    protected $tester;

    /**
     * @throws \Core\Locator\Dynamic\ServiceNotParseable
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
     */
    protected function _before()
    {
        XerviceConfig::getInstance()->getConfig()->set(
            DataProviderConfig::DATA_PROVIDER_PATHS,
            [
                __DIR__ . '/Schema'
            ]
        );

        XerviceConfig::getInstance()->getConfig()->set(DataProviderConfig::FILE_PATTERN, '*.testprovider.xml');
        $this->getFacade()->generateDataProvider();
    }


    /**
     * @group Xervice
     * @group DataProvider
     * @group Integration
     *
     * @throws \Core\Locator\Dynamic\ServiceNotParseable
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
     */
    public function testGeneration()
    {
        $this->getFacade()->cleanDataProvider();
        $this->assertEquals(
            [
                'DefaultDataProvider.php',
                'TestKeyValueDataProvider.php',
                'TestKeyValueCollectionDataProvider.php',
                'WildcardDataProvider.php'
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
            [],
            $dataProvider->getList()
        );
        $this->assertEquals(
            '',
            $dataProvider->getEmptyText()
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