<?php
namespace Application\Mapper;

use Application\Model\BranchInterface;

interface BranchMapperInterface
{
	/**
	 * Gets the location associated with this mapper's branches.
	 *
	 * @return Application\Model\LocationInterface
	 */
	public function getLocation($locationMapper,$reload=false);

}
?>
