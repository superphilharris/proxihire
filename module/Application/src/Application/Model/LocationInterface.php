<?php
namespace Application\Model;

interface LocationInterface
{
	/**
	 * Returns the location's name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns the location's latitude
	 *
	 * @return float
	 */
	public function getLatitude();

	/**
	 * Returns the location's longitude
	 *
	 * @return float
	 */
	public function getLongitude();

}
?>
