<?php
namespace Application\Model;

class Lessor extends User implements LessorInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		parent::exchangeArray($data);
		$this->url_id = isset($data['url_id']) ? (integer) $data['url_id'] : NULL;
	}
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
