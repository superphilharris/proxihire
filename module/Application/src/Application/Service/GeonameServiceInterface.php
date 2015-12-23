<?php

namespace Application\Service;

use Application\Model\GeonameInterface;

interface GeonameServiceInterface
{
	/**
	 * Returns the geonames that match the specified string.
	 *
	 * @param  string $name The location name to match.
	 * @param  object $number The maximum number of results that we want.
	 * @return array|GeonameInterface
	 */
	public function getGeonamesLike($name, $number=15);

	/**
	 * Gets the closest location to the specified latitude and longitude. Returns 
	 * an array of strings, ordered by proximity.
	 *
	 * @param float $latitude
	 * @param float $longitude
	 * @return array|string
	 */
	public function getClosestLocation( $latitude, $longitude);
}
?>
