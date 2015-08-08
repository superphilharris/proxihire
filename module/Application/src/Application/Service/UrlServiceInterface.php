<?php
namespace Application\Service;

use Application\Mapper\UrlMapperInterface;

interface UrlServiceInterface
{
	/**
	 * Returns the url with the specified id
	 *
	 * @param  int $id The url's id
	 * @return UrlInterface
	 */
	public function getUrl($id);
}
?>
