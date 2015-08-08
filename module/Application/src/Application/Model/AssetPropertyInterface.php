<?php
namespace Application\Model;

interface AssetPropertyInterface
{
	/**
	 * Sets the property name
	 *
	 * @param string $price The property name
	 */
	public function setName($name);

	/**
	 * Returns the property name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Sets the property type
	 *
	 * @param string $price The property type
	 */
	public function setPropertyType($type);

	/**
	 * Returns the property type
	 *
	 * @return string
	 */
	public function getPropertyType();

	/**
	 * Sets the property value
	 *
	 * @param mixed $price The property value
	 */
	public function setValue($value);

	/**
	 * Returns the property value
	 *
	 * @return mixed
	 */
	public function getValue();

}
?>
