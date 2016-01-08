<?php
namespace ScraperImporter\Service;

interface ImporterServiceHelperInterface {

	/**
	 * Throws an exception that displays the problem a bit better when decoding json
	 * @param unknown $json
	 * @throws \Exception
	 * @return mixed
	 */
	public function jsonDecode($json);
	
	/**
	 * This fetches an image of a crawled site and puts it into the /public/img/assets/ folder
	 * @param string $url
	 * @return boolean
	 */
	public function syncImage($url, $type="assets");
	
	/**
	 * This determines the latitude, longitude, email address and parses the phone number
	 * @param stdClass $location
	 * @return stdClass
	 */
	public function determineBranch($location, $lessor);
	
	/**
	 * Resizes and crops an image 
	 * @param string $imagePath
	 * @param integer $x
	 * @param integer $y
	 * @return string 	- the new image url
	 */
	public function resizeAndCropImage($imagePath, $x=120, $y=120);
	
	/**
	 * This puts a border around a favicon.ico and will also generate a marker for google maps.
	 * The marker for google maps is the same, except that it ends in ico_marker.ico
	 * @param string $iconPath
	 * @return NULL|string
	 */
	public function createIcons($iconPath);
	
	/**
	 * This routine attempt to extract out properties from the title of the asset.
	 * @param string $assetName
	 * @param array $mainProperties - the properties that we'd expect from this asset
	 * @return array - the properties has
	 */
	public function extractPropertiesFromAssetName($assetName, $mainProperties);
	
	public function determineProperties($properties, $categoryName, $assetName, $mainProperties);

	public function determineRates($rates);

	/**
	 * Determines the category by looking at categories.js file
	 * @param CategoryAliases $category
	 * @param string $name
	 * @return Ambigous Category|NULL
	 */
	public function determineCategory($category, $name);
}
?>
