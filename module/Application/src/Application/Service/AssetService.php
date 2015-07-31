<?php

namespace Application\Service;

use Application\Model\Asset;

class AssetService implements AssetServiceInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getAssetsInCategory($category,$filters=NULL)
	{
		// TODO: implement
		return array(
			$this->getAsset(1),
			$this->getAsset(2),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAsset($id)
	{
		// TODO: implement
		$asset = new Asset();
		// $asset->setCategory("ladders");
		$asset->setId($id);
		return $asset;
	}
}
?>
