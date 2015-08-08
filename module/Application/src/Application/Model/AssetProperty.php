<?php
namespace Application\Model;

class AssetProperty extends AbstractModel implements AssetPropertyInterface
{
	private $id;
	private $name;
	private $datatype;
	private $datatype_id;
	private $asset;
	private $asset_id;
	private $value;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (integer) $data['id'] : NULL;
		$this->name = isset($data['name']) ? (string) $data['name'] : NULL;
		$this->datatype_id = isset($data['datatype_id']) ? (integer) $data['datatype_id'] : NULL;
		$this->asset_id = isset($data['asset_id']) ? (integer) $data['asset_id'] : NULL;
		$this->value = isset($data['value']) ? $data['value'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setId($id)
	{
		// jih: make sure that everythig is in the interface.
		// jih: all of these should be classhelpered.
		$this->id=$id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAsset($asset)
	{
		// jih: make sure that everythig is in the interface.
		// jih: all of these should be classhelpered.
		$this->asset=$asset;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAsset()
	{
		return $this->asset;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName($name)
	{
		$this->name=$name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPropertyType($type)
	{
		// jih: this should be an object.
		$this->datatype=$type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPropertyType()
	{
		return $this->datatype;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValue()
	{
		return $this->value;
	}

}
?>
