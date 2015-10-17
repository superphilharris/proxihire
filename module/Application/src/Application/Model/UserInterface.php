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
	 * @param Application\Model\BranchInterface $location The user's location
	 */
	public function setBranches($branchArray);

	/**
	 * Gets the user's id
	 *
	 * @return int
	 */
	public function getId();
}
?>
