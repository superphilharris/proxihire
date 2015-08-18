<?php
namespace Application\Mapper;

use Application\Model\UserInterface;

interface UserMapperInterface
{
	/**
	 * Gets the urls associated with this mapper's users.
	 *
	 * @return array|Application\Model\UrlInterface
	 */
	public function getUrls($urlMapper,$reload=false);

}
?>
