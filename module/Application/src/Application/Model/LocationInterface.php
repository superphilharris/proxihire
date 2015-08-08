<?php
namespace Application\Model;

interface LocationInterface
{
	/**
	 * Used by Zend\Db's TableGateway class
	 *
	 * @param array $data The data in $key => value pairs
	 */
	public function exchangeArray($data);

	/**
	 * Sets the location's id
	 *
	 * @param int $id The location's id
	 */
	public function setId(int $id);

	/**
	 * Returns the location's id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Sets the location's name
	 *
	 * @param string $name The location's name
	 */
	public function setName(string $name);

	/**
	 * Returns the location's name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Sets the location's latitude
	 *
	 * @param float $latitude The location's latitude
	 */
	public function setLatitude(float $latitude);

	/**
	 * Returns the location's latitude
	 *
	 * @return float
	 */
	public function getLatitude();

	/**
	 * Sets the location's longitude
	 *
	 * @param float $longitude The location's longitude
	 */
	public function setLongitude(float $longitude);

	/**
	 * Returns the location's longitude
	 *
	 * @return float
	 */
	public function getLongitude();

}
?>
