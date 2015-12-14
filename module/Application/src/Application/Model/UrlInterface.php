<?php
namespace Application\Model;

interface UrlInterface
{
	/**
	 * Returns the URL title 
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * Returns the URL path 
	 *
	 * @return string
	 */
	public function getPath();
	
	/**
	 * Returns the URL path to display 
	 *
	 * @return string
	 */
	public function getDisplayPath();

	/**
	 * Increments the click count
	 */
	public function incrementClicks();

	/**
	 * Returns the number of times that the URL has been clicked 
	 *
	 * @return string
	 */
	public function getClicks();

}
?>
