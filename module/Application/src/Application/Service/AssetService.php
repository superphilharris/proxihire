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
		$assets = array();
		for($i=1; $i<100; $i++){
			array_push($assets, $this->getAsset($i));
		}
		return $assets;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAsset($id)
	{
		// TODO: implement
		$properties = array();
		array_push($properties, array("length" => rand(1,100)/10));
		array_push($properties, array("type" => rand(0,1)==1 ? "double":"multi"));
		
		
		$asset = new Asset();
		// $asset->setCategory("ladders");
		$asset->setId($id);
		$asset->setProperties($properties);
		return $asset;
	}
}
?>
