<?php
namespace Application\Model;

interface BranchInterface
{
	/**
	 * Returns the branch's location id
	 *
	 * @return integer
	 */
	public function getLocationId();

	/**
	 * Returns the branch's location
	 *
	 * @return Application\Model\LocationInterface
	 */
	public function getLocation();

	/**
	 * Sets the branch's location
	 *
	 * @param Application\Model\LocationInterface $location The branch's location
	 */
	public function setLocation($location);
}
?>