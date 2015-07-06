<?php
namespace Application\Model;

interface CategoryInterface
{
	/**
	 * Used by Zend\Db's TableGateway class
	 *
	 * @param array $data The data in $key => value pairs
	 */
	public function exchangeArray($data);

	/**
	 * Sets the category's id
	 *
	 * @param int $id The category's id
	 */
	public function setId(int $id);

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
