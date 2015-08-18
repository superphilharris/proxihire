<?php
namespace Application\Mapper;

use Application\Model\LessorInterface;

interface LessorMapperInterface
{
	/**
	 * Gets the locations associated with this mapper's lessors.
	 *
	 * @return array|Application\Model\LocationInterface
	 */
	public function getLocations($locationMapper,$reload=false);
}
?>
