<?php
namespace DataProvider;

/**
 * Auto generated data provider
 */
final class KeyValueDataProvider extends \Xervice\DataProvider\DataProvider\AbstractDataProvider
{
	/** @var string */
	protected $Key;

	/** @var string */
	protected $Value;

	/** @var string */
	protected $Description;


	/**
	 * @return string
	 */
	public function getKey(): string
	{
		return $this->Key;
	}


	/**
	 * @param string $Key
	 * @return KeyValueDataProvider
	 */
	public function setKey(string $Key)
	{
		$this->Key = $Key;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getValue(): string
	{
		return $this->Value;
	}


	/**
	 * @param string $Value
	 * @return KeyValueDataProvider
	 */
	public function setValue(string $Value)
	{
		$this->Value = $Value;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->Description;
	}


	/**
	 * @param string $Description
	 * @return KeyValueDataProvider
	 */
	public function setDescription(string $Description)
	{
		$this->Description = $Description;

		return $this;
	}


	/**
	 * @return array
	 */
	protected function getElements(): array
	{
		return array (
		  'Key' =>
		  array (
		    'name' => 'Key',
		    'allownull' => false,
		    'type' => 'string',
		    'is_collection' => false,
		    'is_dataprovider' => false,
		  ),
		  'Value' =>
		  array (
		    'name' => 'Value',
		    'allownull' => false,
		    'type' => 'string',
		    'is_collection' => false,
		    'is_dataprovider' => false,
		  ),
		  'Description' =>
		  array (
		    'name' => 'Description',
		    'allownull' => false,
		    'type' => 'string',
		    'is_collection' => false,
		    'is_dataprovider' => false,
		  ),
		);
	}
}
