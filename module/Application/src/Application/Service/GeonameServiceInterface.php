<?php

namespace Application\Service;

// jih: uncomment this
//use Application\Model\GeonameInterface;

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
}
?>
