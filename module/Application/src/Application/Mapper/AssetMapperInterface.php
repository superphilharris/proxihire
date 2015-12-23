<?php
namespace Application\Mapper;

use Application\Model\AssetInterface;

interface AssetMapperInterface
{
	/**
	 * Finds all of the assets in the specified category.
	 *
	 * @param Application\Model\CategoryInterface $category
	 * @return array|AssetInterface
	 */
	public function findByCategory( $category, $filters=NULL );

	/**
	 * Returns the array of urls that correspond with those of the assets.
	 *
	 * @param Application\Mapper\UrlMapper $urlMapper The urlMapper used to 
	 *        populate the urls.
	 * @param boolean $reload If true, then forces a reload from the database.
	 * @return array|Url
	 */
	public function getUrls( $urlMapper, $reload=false );
}
?>
