<?php
namespace Application\Model;

interface UserInterface
{
	/**
	 * Returns the user's name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns the user's location id
	 *
	 * @return integer
	 */
	public function getBranchIds();

	/**
	 * Returns the user's location
	 *
	 * @return Application\Model\BranchInterface
	 */
	public function getBranches();

	/**
	 * Sets the user's location
	 *
	 * @param Application\Model\BranchInterface $branchArray The user's location
	 */
	public function setBranches($branchArray);

	/**
	 * Returns the distance in meters to the provided location
	 * 
	 * @param Application\Model\LocationInterface $location 
	 * @return float 
	 */
	public function getDistanceToClosestBranch($location);

}
?>
