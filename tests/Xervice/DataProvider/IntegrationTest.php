<?php
namespace XerviceTest\DataProvider;

use DataProvider\KeyValueCollectionDataProvider;
use DataProvider\KeyValueDataProvider;
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
                'KeyValueCollectionDataProvider.php',
                'KeyValueDataProvider.php',
            ],
            $this->getFacade()->generateDataProvider()
        );

        $testData = [
            'Key' => 'test',
            'Value' => 'value'
        ];

        $keyValue = new KeyValueDataProvider();
        $keyValue->fromArray($testData);

        $list = new KeyValueCollectionDataProvider();
        $list->addKeyValue($keyValue);

        $resultData = $list->toArray();

        $list = new KeyValueCollectionDataProvider();
        $list->fromArray($resultData);

        $this->assertEquals(
            'test',
            $list->getKeyValues()[0]->getKey()
        );
    }
}