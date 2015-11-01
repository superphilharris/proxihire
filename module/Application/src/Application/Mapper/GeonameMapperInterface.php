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

}
?>
