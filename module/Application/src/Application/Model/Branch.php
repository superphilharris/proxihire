<?php
namespace Application\Model;

class Branch extends AbstractModel implements BranchInterface
{
	protected $id;
	protected $location_id;
	protected $location;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (integer) $data['id'] : NULL;
		$this->location_id = isset($data['location_id']) ? (integer) $data['location_id'] : NULL;
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
