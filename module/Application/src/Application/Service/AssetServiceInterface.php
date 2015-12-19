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

	/**
	 * Retrieves all of the lessors associated with the passed assets.
	 *
	 * It will also associate all of the lessors with their respective assets so 
	 * that no more calls to the database are needed in order to get the asset's 
	 * lessor information.
	 *
	 * @param $assetList - the list of asset objects, passed by reference
	 * @return - an array of lessors, indexed by lessor ID
	 */
	public function getLessorsForAssets( &$assetList);
}
?>
