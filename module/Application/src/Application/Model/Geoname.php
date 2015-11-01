<?php
namespace Application\Model;

use Application\Helper\ClassHelper;

class Geoname extends AbstractModel implements GeonameInterface
{
	protected $id;
	protected $name;
	protected $latitude;
	protected $longitude;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (int) $data['id'] : NULL;
		$this->name = isset($data['name']) ? (string) $data['name'] : NULL;
		$this->latitude = isset($data['latitude']) ? (float) $data['latitude'] : NULL;
		$this->longitude = isset($data['longitude']) ? (float) $data['longitude'] : NULL;
	}
	/**
	 * {@inheritdoc}
	 */
	public function setName($name){
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("string"));
		$this->name=$name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setLatitude($latitude){
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("float"));
		$this->latitude=$latitude;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLatitude(){
		return $this->latitude;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setLongitude($longitude){
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("float"));
		$this->longitude=$longitude;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLongitude(){
		return $this->longitude;
	}
}
?>
