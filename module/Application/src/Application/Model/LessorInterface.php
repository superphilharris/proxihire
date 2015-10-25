<?php
namespace Application\Model;

interface LessorInterface extends UserInterface
{
	/**
	 * Gets the lessor's Url ID
	 *
	 * @return integer
	 */
	public function getUrlId();

	/**
	 * Gets the lessor's Url
	 *
	 * @return Application\Model\UrlInterface
	 */
	public function getUrl();

	/**
	 * Sets the lessor's Url
	 *
	 * @param Application\Model\UrlInterface $url
	 */
	public function setUrl($url);

	/**
	 * Gets the lessor's Icon
	 *
	 * @return string
	 */
	public function getIcon();

	/**
	 * Sets the lessor's Icon
	 *
	 * @param string
	 */
	public function setIcon($icon);
}
?>
