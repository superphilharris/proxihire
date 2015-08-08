<?php
namespace Application\Model;

use Application\Helper\ClassHelper;

use Application\Model\CategoryInterface;
use Application\Model\LessorInterface;
use Application\Model\AssetRateInterface;
use Application\Model\AssetPropertyInterface;
use Application\Model\UrlInterface;

class Asset extends AbstractModel implements AssetInterface
{
	protected $id;
	protected $category;
	protected $category_id;
	protected $lessor;
	protected $lessor_id;
	protected $rate_array;
	protected $rate_id_array;
	protected $property_array;
	protected $property_id_array;
	protected $url;
	protected $url_id;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (int) $data['id'] : NULL;
		$this->category_id = isset($data['category_id']) ? (int) $data['category_id'] : NULL;
		$this->lessor_id = isset($data['lessor_id']) ? (int) $data['lessor_id'] : NULL;
		$this->rate_id_array = isset($data['rate_id_array']) ? $data['rate_id_array'] : NULL;
		$this->property_id_array = isset($data['property_id_array']) ? $data['property_id_array'] : NULL;
		$this->url_id = isset($data['url_id']) ? (int) $data['url_id'] : NULL;

	}

	/**
	 * {@inheritdoc}
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLessor()
	{
		return $this->lessor;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRates()
	{
		return $this->rate_array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setRates($rates)
	{
		$this->rate_array=$rates;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRateIds()
	{
		return $this->rate_id_array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getProperties()
	{
		return $this->property_array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setUrl($url)
	{
		// jih: make sure in interface
		// jih: classhelper
		$this->url=$url;
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
	public function getUrlId()
	{
		// jih: make sure in interface
		return $this->url_id;
	}
}
?>
