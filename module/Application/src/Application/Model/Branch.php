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
	
	/**
	 * {@inheritdoc}
	 */
	public function getDisplayPhoneNumber(){
		if($this->phoneNumber !== null){
			/** 
			 * New Zealand Phone Number
			 * @see https://en.wikipedia.org/wiki/Telephone_numbers_in_New_Zealand
			 */
			if(strpos($this->phoneNumber, '+64') === 0){
				if(strpos($this->phoneNumber, '+642') === 0){ 		// New Zealand Mobile
					return preg_replace('/(\+642)(\d)(\d{3})(\d*)/', '(02$2) $3 $4', $this->phoneNumber);
				}elseif(strpos($this->phoneNumber, '+648') === 0){ 	// New Zealand toll free - 08*
					return preg_replace('/(\+648)(\d{2})(\d*)/', 	'08$2 $3', $this->phoneNumber);
				}elseif(strpos($this->phoneNumber, '+64508') === 0){ // New Zealand toll free - vodaphone 0508*
					return preg_replace('/(\+64508)(\d*)/', 		'0508 $2', $this->phoneNumber);
				}elseif(strpos($this->phoneNumber, '+64900') === 0){ // New Zealand premium rate - 0900*
					return preg_replace('/(\+64900)(\d*)/', 		'0900 $2', $this->phoneNumber);
				}else{												// New Zealand Landline
					return preg_replace('/(\+64)(\d)(\d{3})(\d*)/', '(0$2) $3 $4', $this->phoneNumber);
				}
			}
		}
		return $this->phoneNumber;
	}
}
?>
