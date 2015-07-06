<?php
namespace Application\Model;

interface AssetRateInterface
{
	/**
	 * Sets the rate's price (in dollars)
	 *
	 * @param float $price The rate's price (in dollars)
	 */
	public function setPrice(float $price);

	/**
	 * Returns the rate's price (in dollars)
	 *
	 * @return float
	 */
	public function getPrice();

	/**
	 * Sets the rate's duration (in hours)
	 *
	 * @param int $duration The rate's duration (in hours)
	 */
	public function setDuration(int $duration);

	/**
	 * Returns the rate's duration (in hours)
	 *
	 * @return int
	 */
	public function getDuration();

}
?>
