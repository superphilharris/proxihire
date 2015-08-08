<?php

namespace Application\Service;

use Application\Model\AssetInterface;

interface AssetServiceInterface
{
	/**
	 * Should return the set of assets which match the specified category, 
	 * location, and filters.
	 *
	 * @param  string $category The xpath of the category
	 * @param  object $filters  How to filter the results
	 * @param  string $location A string representation of the location
	 * @return array|AssetInterface[]
	 */
	public function getAssetList($category, $filters=NULL, $location=NULL);
}
?>
