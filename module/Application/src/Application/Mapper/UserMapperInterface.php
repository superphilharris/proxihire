<?php
namespace Application\Mapper;

use Application\Model\UserInterface;

interface UserMapperInterface
{
	/**
	 * Gets the branches associated with this mapper's users.
	 *
	 * @return array|Application\Model\BranchInterface
	 */
	public function getBranches($branchMapper,$reload=false);

}
?>
