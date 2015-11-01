<?php
namespace Application\Model;

interface GeonameInterface
{
	/**
	 * Sets the name of the geoname's location.
	 *
	 * @return string $name
	 *         The human-readable name of the location.
	 */
	public function setName($name);

	/**
	 * Gets the name of the geoname's location.
	 *
	 * @return string
	 *         The human-readable name of the location.
	 */
	public function getName();

	/**
	 * Sets the latitude of the geoname's location
	 *
	 * @return float $latitude
	 *         The latitude of the location.
	 */
	public function setLatitude($latitude);

	/**
	 * Gets the latitude of the geoname's location
	 *
	 * @return float
	 *         The latitude of the location.
	 */
	public function getLatitude();

	/**
	 * Sets the longitude of the geoname's location
	 *
	 * @return float $longitude
	 *         The longitude of the location.
	 */
	public function setLongitude($longitude);

	/**
	 * Gets the longitude of the geoname's location
	 *
	 * @return float
	 *         The longitude of the location.
	 */
	public function getLongitude();
}
?>
