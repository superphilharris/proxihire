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
	 * Sets the asset's lessor
	 *
	 * @param Application\Model\LessorInterface
	 */
	public function setLessor($lessor);

	/**
	 * Returns the asset's lessor's ID
	 *
	 * @return integer
	 */
	public function getLessorId();

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
	 * Sets the url of this asset.
	 *
	 * @param $url Application\Model\UrlInterface
	 */
	public function setUrl($url);

	/**
	 * Returns ID of the the URL object relating to the lessor's listing.
	 *
	 * @return integer
	 */
	public function getUrlId();

	/**
	 * Returns the URL object relating to the lessor's listing.
	 *
	 * @return Application\Model\UrlInterface
	 */
	public function getUrl();
}
?>
