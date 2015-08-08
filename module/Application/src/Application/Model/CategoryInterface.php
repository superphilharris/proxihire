<?php
namespace Application\Model;

interface CategoryInterface
{
	/**
	 * Returns the category's id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Sets the category's name
	 *
	 * @param string $name The category's name
	 */
	public function setName(string $name);

	/**
	 * Returns the category's aliases
	 *
	 * @return array|string
	 */
	public function getAliases();

	/**
	 * Adds an alias to this category
	 *
	 * @param $alias The new alias
	 */
	public function addAlias(string $alias);

	/**
	 * Deletes the specified alias
	 *
	 * @param $alias The alias to delete
	 */
	public function deleteAlias(string $alias);

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
}
?>
