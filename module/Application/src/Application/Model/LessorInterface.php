<?php
namespace Application\Model;

use Application\Model\LocationInterface;
use Application\Model\UrlInterface;

interface LessorInterface extends UserInterface
{
	/**
	 * Sets the lessor's home URL
	 *
	 * @param Application\Model\UrlInterface $url The lessor's URL
	 */
	public function setUrl(UrlInterface $url);

	/**
	 * Returns the lessor's home URL
	 *
	 * @return Application\Model\UrlInterface
	 */
	public function getUrl();

}
?>
