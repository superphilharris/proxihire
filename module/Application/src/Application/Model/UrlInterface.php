<?php
namespace Application\Model;

interface UrlInterface
{
	/**
	 * Sets the URL's printable title 
	 *
	 * @param string $title The title to show when printing the link
	 */
	public function setTitle(string $title);

	/**
	 * Returns the URL title 
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * Sets the URL's path 
	 *
	 * @param string $path The URL path 
	 */
	public function setPath(string $path);

	/**
	 * Returns the URL path 
	 *
	 * @return string
	 */
	public function getPath();

	/**
	 * Sets the number of times that the URL has been clicked 
	 *
	 * @param string $clicks The number of URL clicks 
	 */
	public function setClicks(string $clicks);

	/**
	 * Returns the number of times that the URL has been clicked 
	 *
	 * @return string
	 */
	public function getClicks();

}
?>
