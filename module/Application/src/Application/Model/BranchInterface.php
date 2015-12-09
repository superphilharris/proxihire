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

	/**
	 * Returns the branch's email
	 *
	 * @return string
	 */
	public function getEmail();

	/**
	 * Returns the branch's phone number
	 *
	 * @return string
	 */
	public function getPhoneNumber();

	/**
	 * Displays the phone number in the right locale
	 * @return string
	 */
	public function getDisplayPhoneNumber();
}
?>
