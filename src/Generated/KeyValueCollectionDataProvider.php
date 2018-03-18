<?php
namespace DataProvider;

/**
 * Auto generated data provider
 */
final class KeyValueCollectionDataProvider extends \Xervice\DataProvider\DataProvider\AbstractDataProvider
{
	/** @var \DataProvider\KeyValueDataProvider[] */
	protected $KeyValues;


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
	 * @param KeyValue $KeyValue
	 */
	public function addKeyValue(KeyValueDataProvider $KeyValue)
	{
		$this->KeyValues[] = $KeyValue;
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
		    'singleton' => 'KeyValue',
		    'singleton_type' => '\\DataProvider\\KeyValueDataProvider',
		  ),
		);
	}
}
