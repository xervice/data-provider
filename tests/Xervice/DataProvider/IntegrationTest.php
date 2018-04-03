<?php
namespace XerviceTest\DataProvider;

use DataProvider\TestKeyValueCollectionDataProvider;
use DataProvider\TestKeyValueDataProvider;
use Xervice\Core\Locator\Dynamic\DynamicLocator;

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
     * @group Xervice
     * @group DataProvider
     * @group Integration
     *
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function testGeneration()
    {
        $this->getFacade()->cleanDataProvider();
        $this->assertEquals(
            [
                'TestKeyValueDataProvider.php',
                'TestKeyValueCollectionDataProvider.php',
            ],
            $this->getFacade()->generateDataProvider()
        );

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
    }
}