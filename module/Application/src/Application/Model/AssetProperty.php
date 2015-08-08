<?php
namespace Application\Model;

class AssetProperty extends AbstractModel implements AssetPropertyInterface
{
	private $name;
	private $propertyType;
	private $value;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
	}
	/**
	 * {@inheritdoc}
	 */
	public function setName(string $name)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPropertyType(string $type)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPropertyType()
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue(mixed $value)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValue()
	{
	}

}
?>
