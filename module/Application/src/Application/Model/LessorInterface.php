<?php
namespace Application\Model;

use Application\Model\LocationInterface;
use Application\Model\UrlInterface;

interface LessorInterface extends UserInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getUrlId()
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrl()
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function setUrl($url)
	{
	}

}
?>
