<?php
namespace Application\Model;

interface CategoryInterface
{
	/**
	 * Sets the category's name
	 *
	 * @param string $name The category's name
	 */
	public function setName(string $name);

	/**
	 * Returns the category's aliases
	 *
	 * @return array|Application\Model\CategoryAlias
	 */
	public function getAliases();

	/**
	 * Sets the aliases
	 *
	 * @param $aliases The new aliases
	 */
	public function setAliases($aliases);

	/**
	 * Gets the category's alias IDs
	 *
	 * @return array|integer an array of the alias ids
	 */
	public function getAliasIds();

	/**
	 * Returns the category's name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Sets the category's parent
	 *
	 * @param Application\Model\CategoryInterface $category The category's parent category
	 */
	public function setParent(CategoryInterface $category);

	/**
	 * Returns the category's parent category
	 *
	 * @return Application\Model\CategoryInterface
	 */
	public function getParent();

	/**
	 * Returns an array the category's children categories
	 *
	 * @return array|Application\Model\CategoryInterface
	 */
	public function getChildren();

	/**
	 * Returns the number of times that this category has been loaded from the 
	 * database. A measure of the category's popularity.
	 *
	 * @return integer - the number of times that this category has been loaded
	 */
	public function getLoads();

	/**
	 * Increments the number of times that this category has been loaded from the 
	 * database. A measure of the category's popularity
	 */
	public function incrementLoads();
}
?>
