<?php
namespace Application\Model;

interface AssetInterface
{
	/**
	 * Used by Zend\Db's TableGateway class
	 *
	 * @param array $data The data in $key => value pairs
	 */
	public function exchangeArray($data);

	/**
	 * Sets the asset's category
	 *
	 * @param Application\Model\CategoryInterface $category The category
	 */
	public function setCategory($category);

	/**
	 * Returns the asset's category
	 *
	 * @return Application\Model\CategoryInterface
	 */
	public function getCategory();

	/**
	 * Sets the number of clickthroughs
	 *
	 * @param int $clicks The number of clicks
	 */
	public function setClicks($clicks);

	/**
	 * Returns the accumulation of all click-throughs for this asset.
	 *
	 * @return int
	 */
	public function getClicks();

	/**
	 * Sets the asset's id
	 *
	 * @param int $id The asset's id
	 */
	public function setId($id);

	/**
	 * Returns the asset's id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Sets the asset's lessor
	 *
	 * @param Application\Model\LessorInterface $lessor The asset's lessor
	 */
	public function setLessor($lessor);

	/**
	 * Returns the asset's lessor
	 *
	 * @return Application\Model\LessorInterface
	 */
	public function getLessor();

	/**
	 * Sets the asset's price
	 *
	 * @param Application\Model\AssetPriceInterface $price The asset's price
	 */
	public function setPrice($price);

	/**
	 * Returns the asset's price
	 *
	 * @return Application\Model\AssetPriceInterface
	 */
	public function getPrice();

	/**
	 * Sets the asset's properties
	 *
	 * @param array|Application\Model\AssetPropertyInterface $properties The 
	 *        asset's properties
	 */
	public function setProperties($properties);

	/**
	 * Returns the asset's properties
	 *
	 * @return array|Application\Model\AssetPropertyInterface
	 */
	public function getProperties();

	/**
	 * Sets the URL of the lessor's listing.
	 *
	 * @param string $url The asset's URL
	 */
	public function setUrl($url);

	/**
	 * Returns the URL of the lessor's listing.
	 *
	 * @return string
	 */
	public function getUrl();
}
?>
