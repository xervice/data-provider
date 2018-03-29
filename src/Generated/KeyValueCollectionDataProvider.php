<?php
namespace DataProvider;

/**
 * Auto generated data provider
 */
final class KeyValueCollectionDataProvider extends \Xervice\DataProvider\DataProvider\AbstractDataProvider
{
	/** @var \DataProvider\KeyValueDataProvider[] */
	protected $KeyValues;

	/** @var \DataProvider\KeyValueDataProvider */
	protected $ChildValue;


	/**
	 * @return \DataProvider\KeyValueDataProvider[]
	 */
	public function getKeyValues(): array
	{
		return $this->KeyValues;
	}


	/**
	 * @param \DataProvider\KeyValueDataProvider[] $KeyValues
	 * @return KeyValueCollectionDataProvider
	 */
	public function setKeyValues(array $KeyValues)
	{
		$this->KeyValues = $KeyValues;

		return $this;
	}


	/**
	 * @param \DataProvider\KeyValueDataProvider $OneValue
	 */
	public function addOneValue(KeyValueDataProvider $OneValue)
	{
		$this->KeyValues[] = $OneValue;
	}


	/**
	 * @return \DataProvider\KeyValueDataProvider
	 */
	public function getChildValue(): KeyValueDataProvider
	{
		return $this->ChildValue;
	}


	/**
	 * @param \DataProvider\KeyValueDataProvider $ChildValue
	 * @return KeyValueCollectionDataProvider
	 */
	public function setChildValue(KeyValueDataProvider $ChildValue)
	{
		$this->ChildValue = $ChildValue;

		return $this;
	}


	/**
	 * @param \DataProvider\KeyValueDataProvider $ChildValue
	 */
	public function addChildValue(KeyValueDataProvider $ChildValue)
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
		    'type' => '\\DataProvider\\KeyValueDataProvider[]',
		    'is_collection' => true,
		    'is_dataprovider' => false,
		    'singleton' => 'OneValue',
		    'singleton_type' => '\\DataProvider\\KeyValueDataProvider',
		  ),
		  'ChildValue' =>
		  array (
		    'name' => 'ChildValue',
		    'type' => '\\DataProvider\\KeyValueDataProvider',
		    'is_collection' => false,
		    'is_dataprovider' => true,
		    'singleton' => 'ChildValue',
		    'singleton_type' => '\\DataProvider\\KeyValueDataProvider',
		  ),
		);
	}
}
