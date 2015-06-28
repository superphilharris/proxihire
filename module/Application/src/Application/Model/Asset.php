<?php
namespace Application\Model;

class Asset implements AssetInterface
{
	public $category;
	public $clicks;
	public $id;
	public $lessor;
	public $price;
	public $properties;
	public $url;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->setCategory(  (!empty($data['category']))   ? $data['category']   : null);
		$this->setClicks(    (!empty($data['clicks']))     ? $data['clicks']     : null);
		$this->setId(        (!empty($data['id']))         ? $data['id']         : null);
		$this->setLessor(    (!empty($data['lessor']))     ? $data['lessor']     : null);
		$this->setPrice(     (!empty($data['price']))      ? $data['price']      : null);
		$this->setProperties((!empty($data['properties'])) ? $data['properties'] : null);
		$this->setUrl(       (!empty($data['url']))        ? $data['url']        : null);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setCategory($category){
		$this->category=$category;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCategory(){
		return $this->category;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setClicks($clicks){
		$this->clicks=$clicks;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getClicks(){
		return $this->clicks;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setId($id){
		$this->id=$id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setLessor($lessor){
		$this->lessor=$lessor;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLessor(){
		return $this->lessor;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrice($price){
		$this->price=$price;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPrice(){
		return $this->price;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setProperties($properties){
		$this->properties=$properties;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getProperties(){
		return $this->properties;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setUrl($url){
		$this->url=$url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrl(){
		return $this->url;
	}
}
?>
