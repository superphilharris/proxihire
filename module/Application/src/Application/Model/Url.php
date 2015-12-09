<?php
namespace Application\Model;

class Url extends AbstractModel implements UrlInterface
{
	protected $id;
	protected $path;
	protected $title;
	protected $clicks;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? $data['id'] : NULL;
		$this->path = isset($data['path']) ? $data['path'] : NULL;
		$this->title = isset($data['title']) ? $data['title'] : NULL;
		$this->clicks = isset($data['clicks']) ? $data['clicks'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDisplayPath()
	{
		if($this->path !== null){
			$prefix = preg_replace('/http\s*:\/\/([^\/]*)\/.*/', '$1/...', $this->path);
			return $prefix . substr($this->path, -15);
		}
		return $this->path;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getClicks()
	{
		return $this->clicks;
	}

	/**
	 * {@inheritdoc}
	 */
	public function incrementClicks()
	{
		$this->clicks = $this->clicks + 1;
	}
}
?>
