<?php
namespace Application\Mapper;

use Application\Model\LessorInterface;

interface LessorMapperInterface
{
	/**
	 * Gets the url associated with this mapper's lessors.
	 *
	 * @return Application\Model\UrlInterface
	 */
	public function getUrl($urlapper,$reload=false);
}
?>
