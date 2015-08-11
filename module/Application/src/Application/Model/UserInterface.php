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
	public function getLocationId();

	/**
	 * Returns the user's location
	 *
	 * @return Application\Model\LocationInterface
	 */
	public function getLocation();

	/**
	 * Sets the user's location
	 *
	 * @param Application\Model\LocationInterface $location The user's location
	 */
	public function setLocation($location);
}
?>
