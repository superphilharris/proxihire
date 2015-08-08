<?php
namespace Application\Model;

use Application\Model\CategoryInterface;
use Application\Model\LessorInterface;
use Application\Model\AssetRateInterface;
use Application\Model\AssetPropertyInterface;
use Application\Model\UrlInterface;

interface AssetInterface
{

	/**
	 * Returns the asset's category
	 *
	 * @return Application\Model\CategoryInterface
	 */
	public function getCategory();

	/**
	 * Returns the asset's id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Returns the asset's lessor
	 *
	 * @return Application\Model\LessorInterface
	 */
	public function getLessor();

	/**
	 * Sets the asset's rates
	 *
	 * @param array|Application\Model\AssetRateInterface $rate
	 */
	public function setRates($rates);

	/**
	 * Returns the asset's rate ids
	 *
	 * @return array|integer
	 */
	public function getRateIds();

	/**
	 * Returns the asset's rate
	 *
	 * @return array|Application\Model\AssetRateInterface
	 */
	public function getRates();

	/**
	 * Returns the asset's properties
	 *
	 * @return array|Application\Model\AssetPropertyInterface
	 */
	public function getProperties();

	/**
	 * Returns the URL of the lessor's listing.
	 *
	 * @return Application\Model\UrlInterface
	 */
	public function getUrl();
}
?>
