<?php
namespace Application\Model;

class User extends AbstractModel implements UserInterface
{
	protected $id;
	protected $name;
	protected $location_id;
	protected $location;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (integer) $data['id'] : NULL;
		$this->name = isset($data['name']) ? (string) $data['name'] : NULL;
		$this->location_id = isset($data['location_id']) ? (integer) $data['location_id'] : NULL;
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
	public function getLocationId()
	{
		return (integer) $this->location_id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setLocation($location)
	{
		// jih: classhelper
		$this->location = $location;
	}
}
?>
