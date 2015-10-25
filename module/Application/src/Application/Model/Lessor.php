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
		$this->icon_url	= isset($data['icon_url']) ? $data['icon_url'] : NULL;
	}
	/**
	 * {@inheritdoc}
	 */
	public function getUrlId()
	{
		return (integer) $this->url_id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIcon()
	{
		return $this->icon_url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setIcon($icon)
	{
		$this->icon_url = $icon;
	}

}
?>
