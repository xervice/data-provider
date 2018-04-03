<?php
namespace DataProvider;

/**
 * Auto generated data provider
 */
final class TestKeyValueCollectionDataProvider extends \Xervice\DataProvider\DataProvider\AbstractDataProvider
{
	/** @var \DataProvider\TestKeyValueDataProvider[] */
	protected $KeyValues;

	/** @var \DataProvider\TestKeyValueDataProvider */
	protected $ChildValue;


	/**
	 * @return \DataProvider\TestKeyValueDataProvider[]
	 */
	public function getKeyValues(): array
	{
		return $this->KeyValues;
	}


	/**
	 * @param \DataProvider\TestKeyValueDataProvider[] $KeyValues
	 * @return TestKeyValueCollectionDataProvider
	 */
	public function setKeyValues(array $KeyValues)
	{
		$this->KeyValues = $KeyValues;

		return $this;
	}


	/**
	 * @param \DataProvider\TestKeyValueDataProvider $OneValue
	 */
	public function addOneValue(TestKeyValueDataProvider $OneValue)
	{
		$this->KeyValues[] = $OneValue;
	}


	/**
	 * @return \DataProvider\TestKeyValueDataProvider
	 */
	public function getChildValue(): TestKeyValueDataProvider
	{
		return $this->ChildValue;
	}


	/**
	 * @param \DataProvider\TestKeyValueDataProvider $ChildValue
	 * @return TestKeyValueCollectionDataProvider
	 */
	public function setChildValue(TestKeyValueDataProvider $ChildValue = null)
	{
		$this->ChildValue = $ChildValue;

		return $this;
	}


	/**
	 * @param \DataProvider\TestKeyValueDataProvider $ChildValue
	 */
	public function addChildValue(TestKeyValueDataProvider $ChildValue)
	{
		$this->ChildValue[] = $ChildValue;
	}


	/**
	 * @return array
	 */
	protected function getElements(): array
	{
		return array (
		  'KeyValues' =>
		  array (
		    'name' => 'KeyValues',
		    'allownull' => false,
		    'default' => '',
		    'type' => '\\DataProvider\\TestKeyValueDataProvider[]',
		    'is_collection' => true,
		    'is_dataprovider' => false,
		    'singleton' => 'OneValue',
		    'singleton_type' => '\\DataProvider\\TestKeyValueDataProvider',
		  ),
		  'ChildValue' =>
		  array (
		    'name' => 'ChildValue',
		    'allownull' => true,
		    'default' => '',
		    'type' => '\\DataProvider\\TestKeyValueDataProvider',
		    'is_collection' => false,
		    'is_dataprovider' => true,
		    'singleton' => 'ChildValue',
		    'singleton_type' => '\\DataProvider\\TestKeyValueDataProvider',
		  ),
		);
	}
}
