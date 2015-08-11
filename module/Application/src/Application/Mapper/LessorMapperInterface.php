<?php
namespace Application\Mapper;

use Application\Model\LessorInterface;

interface LessorMapperInterface
{
	/**
	 * Gets the urls associated with this mapper's lessors.
	 *
	 * @return array|Application\Model\UrlInterface
	 */
	public function getUrls($urlMapper,$reload=false);
}
?>
