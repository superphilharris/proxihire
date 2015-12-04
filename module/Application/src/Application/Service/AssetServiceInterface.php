<?php

namespace Application\Service;

use Application\Model\AssetInterface;

interface AssetServiceInterface
{
	/**
	 * Should return the set of assets which match the specified category.
	 *
	 * @param  string $category The xpath of the category
	 * @return array|AssetInterface[]
	 */
	public function getAssetList($category,$allCategoryAliases);
}
?>
