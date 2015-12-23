<?php
namespace Application\Mapper;

use Application\Model\CategoryInterface;

interface CategoryMapperInterface
{
	/**
	 * Find the category by name.
	 *
	 * @param string $categoryName
	 * @return CategoryInterface
	 */
	public function findByName( $categoryName );

	/**
	 * Gets the popular categories for each catogory specified in the 
	 * categoryList array
	 *
	 * @param array $categoryList - An array of strings representing each of the 
	 *                              category names
	 * @param int   $limit        - The maximum number of categories to return
	 * @return array|Application\Model\CategoryInterface
	 *                            - An array of categoryInterfaces
	 */
	public function getPopularCategories( $categoryList, $limit=5 );
}
?>
