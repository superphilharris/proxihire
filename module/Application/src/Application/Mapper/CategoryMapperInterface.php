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
}
?>
