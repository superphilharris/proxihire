<?php
namespace Application\Mapper;

use Application\Model\GeonameInterface;

interface GeonameMapperInterface
{
	/**
	 * Finds all the geonames whose name contains '$name'.
	 *
	 * @param  string $name
	 * @param  object $number The maximum number of results that we want.
	 * @return array|GeonameInterface
	 */
	public function findLike( $name, $number );

	/**
	 * Gets the closest location to the specified latitude and longitude.
	 *
	 * Also allows an argument "radius" which limits the locations to anything 
	 * within the specified lat/long tolerance.
	 *
	 * @param float $latitude  - The latitude to mathc
	 * @param float $longitude - The longitude to match
	 * @param float $radius    - The maximum bounds to location
	 * @return array|string - An array of strings representing the closest 
	 *                        locations. Ordered by proximity
	 */
	public function getClosestLocation( $latitude, $longitude, $radius=50.0 );

}
?>
