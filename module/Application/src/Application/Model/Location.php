<?php
namespace Application\Model;

class Location extends AbstractModel implements LocationInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (integer) $data['id'] : NULL;
		$this->name = isset($data['name']) ? (string) $data['name'] : NULL;
		$this->latitude = isset($data['latitude']) ? (float) $data['latitude'] : NULL;
		$this->longitude = isset($data['longitude']) ? (float) $data['longitude'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return (string) $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLatitude()
	{
		return (float) $this->latitude;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLongitude()
	{
		return (float) $this->longitude;
	}

}
?>
