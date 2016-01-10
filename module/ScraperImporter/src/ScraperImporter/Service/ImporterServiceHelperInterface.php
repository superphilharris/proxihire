<?php
namespace ScraperImporter\Service;

interface ImporterServiceHelperInterface {

	/**
	 * Wrapper for `json_decode`.
	 *
	 * If unsuccessful in decoding the json, then this method throws an exception 
	 * with a little more information to help in debugging.
	 *
	 * @param unknown $json
	 * @throws \Exception
	 * @return mixed
	 */
	public function jsonDecode($json);
	
	/**
	 * Fetches an image of a crawled site and puts it into the /public/img/assets/ folder
	 *
	 * Under any of the following conditions, we do not actually download the 
	 * image:
	 *
	 * 1. If the private property `isCategorizeOnly` is set to true OR
	 * 2. If 
	 *    a. The file already exists AND
	 *    b. $this:UPDATE_IMAGES evaluates false
	 *
	 * @param  string $url  - The url of the image to download
	 * @param  string $type - If defined, then will place the downloaded image 
	 *                        into `/public/img/$type/`. By default, set to 
	 *                        `assets`.
	 * @return string       - the local path to the image. NULL if unsuccessful.
	 */
	public function syncImage($url, $type="assets");
	
	/**
	 * Gets branch information from the scraped `lessor` and `location` objects.
	 *
	 * Takes in the scraped lessor object, the scraped location object, and 
	 * returns a cleaned-up object containing standard branch information. The 
	 * returned information will be from the `$location` object if specified, 
	 * else from the `$lessor` object. If neither are specified, then the value 
	 * will default to NULL.
	 * 
	 * Specifically, we have
	 *
	 * - `lat` The latitude
	 * - `long` The longitude
	 * - `email`
	 * - `phone_number` This is also converted to a standard format `+649xxxxxxx`
	 * - `name`
	 *
	 * ### Special handling of fields
	 *
	 * #### Lat/Long
	 *
	 * `lat` and `long` are handled slightly differently from the other 
	 * properties. If `$location->lat` and `$location->long` are defined, then 
	 * these are simply used. However, if 
	 *
	 * - `$location` is a string OR
	 * - `$location->address` is a string
	 *
	 * then this is assumed to be a street address. Google is then used to 
	 * determine the `lat` and `long` values.
	 *
	 * #### Email/Phone
	 *
	 * If the email address or phone number are blank, then we use Bing to try to 
	 * determine them.
	 *
	 * @param  mixed    $location - Either an object containing the 
	 *                              branch-specific details, otherwise a string 
	 *                              specifying the branch address
	 * @param  stdClass $lessor   - The scraped `lessor` object.
	 * @return stdClass
	 */
	public function determineBranch($location, $lessor);
	
	/**
	 * Resizes and crops an image to a standard size
	 *
	 * Be default, the image will be converted to 120 x 120 pixels.
	 *
	 * @param string $imagePath - The path to the old image
	 * @param integer $x        - what the final width should be in pixels. 
	 *                            Default 120.
	 * @param integer $y        - What the final height should be in pixels. 
	 *                            Default 120.
	 * @return string           - the new image url
	 */
	public function resizeAndCropImage($imagePath, $x=120, $y=120);
	
	/**
	 * Standardize the icon format.
	 *
	 * This puts a border around a favicon.ico and will also generate a marker 
	 * for google maps. The marker for google maps is the same, except that it 
	 * ends in ico_marker.ico
	 *
	 * @param string $iconPath - The path to the old icon
	 * @return NULL|string     - The path to the new icon
	 */
	public function createIcons($iconPath);
	
	/**
	 * Returns the properties of the scraped objects
	 *
	 * This method will attempt to determine the properties based on those 
	 * scraped. It returns an array of objects with the following fields
	 *
	 * - `name_fulnam` => The name of the property as it should appear *in our 
	 *    database*
	 *
	 * The keys used for this array is name of the property as used by the site.
	 *
	 * @param  array  $properties     - the scraped properties in `name`=>`value` 
	 *                                  pairs
	 * @param         $categoryName   - This is the name of the category of 
	 *                                  asset. Because different sites will use 
	 *                                  different names for each of the 
	 *                                  properties, this is used to look up a 
	 *                                  table containing mappings of different 
	 *                                  potential property names for the same 
	 *                                  category of asset across sites.
	 * @param  string $assetName      - The scraped name of the asset. Because 
	 *                                  some sites will actually include key 
	 *                                  properties when naming assets, we also 
	 *                                  use the scraped name to find out more 
	 *                                  information on the properties of the 
	 *                                  asset.
	 * @param  array  $mainProperties - an array of the expected properties for 
	 *                                  this category. Typically from the 
	 *                                  `/js/categories.js` file
	 * @return 
	 */
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
