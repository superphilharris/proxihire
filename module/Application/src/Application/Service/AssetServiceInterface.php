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
	public function getAssetsInCategory($category,$filters=NULL);

	/**
	 * Returns the asset with the specified id
	 *
	 * @param  int $id The asset's id
	 * @return AssetInterface
	 */
	public function getAsset($id);
}
?>
