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
	protected $image_url;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (int) $data['id'] : NULL;
		$this->category_id = isset($data['category_id']) ? (int) $data['category_id'] : NULL;
		$this->lessor_id = isset($data['lessor_id']) ? (int) $data['lessor_id'] : NULL;
		$this->rate_id_array = isset($data['rate_id_array']) ? $data['rate_id_array'] : array();
		$this->property_id_array = isset($data['property_id_array']) ? $data['property_id_array'] : array();
		$this->url_id = isset($data['url_id']) ? (int) $data['url_id'] : NULL;
		$this->image_url = isset($data['image_url']) ? $data['image_url'] : NULL;
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
	public function setLessor($lessor)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Model\LessorInterface"
		));

		$this->lessor=$lessor;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLessorId()
	{
		return $this->lessor_id;
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
		if( is_null($this->rate_array) ) $this->rate_array = array();
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
		if( !is_array( $this->rate_id_array ) ){
			$this->rate_id_array = array($this->rate_id_array);
		}
		foreach( $this->rate_id_array as $key => $id ){
			$this->rate_id_array[$key] = (integer) $id;
		}
		return $this->rate_id_array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getProperties()
	{
		if( is_null($this->property_array) ) $this->property_array = array();
		return $this->property_array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setProperties($properties)
	{
		$this->property_array=$properties;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPropertyIds()
	{
		if( !is_array( $this->property_id_array ) ){
			$this->property_id_array = array($this->property_id_array);
		}
		foreach( $this->property_id_array as $key => $id ){
			$this->property_id_array[$key] = (integer) $id;
		}
		return $this->property_id_array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setUrl($url)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Model\UrlInterface"
		));
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
		return (integer) $this->url_id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setImage($image)
	{
		$this->image_url=$image;
	}
	/**
	 * {@inheritdoc}
	 */
	public function getImage()
	{
		return $this->image_url;
	}
	/**
	 * {@inheritdoc}
	 */
	public function getImageForSize($x, $y)
	{
		if($this->image_url !== null){
			$imagePathParts = explode('.', $this->image_url);
			return 'assets/' . implode('.', array_splice($imagePathParts, 0, -1))."_".$x."x".$y.".".end($imagePathParts);
		}
		return 'no_image_'.$x."x".$y.'.png';
	}
}
?>
