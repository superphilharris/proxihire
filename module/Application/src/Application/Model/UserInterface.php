<?php
namespace Application\Model;

interface UserInterface
{
	/**
	 * Used by Zend\Db's TableGateway class
	 *
	 * @param array $data The data in $key => value pairs
	 */
	public function exchangeArray($data);

	/**
	 * Sets the user's id
	 *
	 * @param int $id The user's id
	 */
	public function setId(int $id);

	/**
	 * Returns the user's id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Sets the user's name
	 *
	 * @param string $name The user's name
	 */
	public function setName(string $name);

	/**
	 * Returns the user's name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Sets the user's location
	 *
	 * @param Application\Model\LocationInterface $location The user's location
	 */
	public function setLocation(LocationInterface $location);

	/**
	 * Returns the user's location
	 *
	 * @return Application\Model\LocationInterface
	 */
	public function getLocation();
}
?>
