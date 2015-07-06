<?php
namespace Application\Model;

use Application\Helper\ClassHelper;

use Application\Model\CategoryInterface;
use Application\Model\LessorInterface;
use Application\Model\AssetRateInterface;
use Application\Model\AssetPropertyInterface;
use Application\Model\UrlInterface;

class Asset implements AssetInterface
{
	public $category;
	public $clicks;
	public $id;
	public $lessor;
	public $rate;
	public $properties;
	public $url;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		//$this->setCategory(  (!empty($data['category']))   ? $data['category']   : null);
		$this->setClicks(    (!empty($data['clicks']))     ? $data['clicks']     : null);
		$this->setId(        (!empty($data['id']))         ? $data['id']         : null);
		//$this->setLessor(    (!empty($data['lessor']))     ? $data['lessor']     : null);
		//$this->setRate(      (!empty($data['rate']))      ? $data['rate']      : null);
		//$this->setProperties((!empty($data['properties'])) ? $data['properties'] : null);
		//$this->setUrl(       (!empty($data['url']))        ? $data['url']        : null);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setCategory(CategoryInterface $category)
	{
		$this->category=$category;
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
	public function incrementClicks()
	{
		$this->clicks=$this->clicks+1;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setClicks($clicks)
	{
		ClassHelper::checkAllArguments(__METHOD__,func_get_args(),array("integer"));
		$this->clicks=$clicks;
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
	public function setId($id)
	{
		ClassHelper::checkAllArguments(__METHOD__,func_get_args(),array("integer"));
		$this->id=$id;
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
	public function setLessor(LessorInterface $lessor)
	{
		$this->lessor=$lessor;
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
	public function setRate(AssetRateInterface $rate)
	{
		$this->rate=$rate;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRate()
	{
		return $this->rate;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setProperties(array $properties)
	{
		$this->properties=$properties;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getProperties()
	{
		return $this->properties;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setUrl(UrlInterface $url)
	{
		$this->url=$url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrl()
	{
		return $this->url;
	}
}
?>
