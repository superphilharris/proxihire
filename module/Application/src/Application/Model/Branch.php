<?php
namespace Application\Model;

class Branch extends AbstractModel implements BranchInterface
{
	protected $id;
	protected $location_id;
	protected $location;
	protected $email;
	protected $phoneNumber;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id 			= isset($data['id']) 				? (integer) $data['id'] 				: NULL;
		$this->location_id 	= isset($data['location_id']) 		? (integer) $data['location_id'] 		: NULL;
		$this->email 		= isset($data['email_email']) 		? 			$data['email_email'] 		: NULL;
		$this->phoneNumber 	= isset($data['phone_number_text']) ? 			$data['phone_number_text'] 	: NULL;
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

	/**
	 * {@inheritdoc}
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPhoneNumber()
	{
		return $this->phoneNumber;
	}
}
?>
