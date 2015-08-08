<?php

namespace Application\Service;

use Application\Mapper\CategoryMapperInterface;

interface CategoryServiceInterface
{
	/**
	 * Returns the category based on its name.
	 *
	 * @param  string $category The name of the category
	 * @return CategoryInterface
	 */
	public function getCategoryByName($category);

	/**
	 * Returns the category with the specified id
	 *
	 * @param  int $id The category's id
	 * @return CategoryInterface
	 */
	public function getCategory($id);
}
?>
