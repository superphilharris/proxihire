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

	/**
	 * {@inheritdoc}
	 */
	public function getDistanceTo($location){
		if(is_float($location->getLatitude()) AND is_float($location->getLongitude()) AND is_float($this->latitude) AND is_float($this->longitude)){
			$earthRadius 	= 6371000;
			$dLat			= deg2rad($location->getLatitude()  - $this->latitude);
			$dLon 			= deg2rad($location->getLongitude() - $this->longitude);
			$a = 	sin($dLat/2) 					* sin($dLat/2) +
					cos(deg2rad($this->latitude)) 	* cos(deg2rad($location->getLatitude())) *
					sin($dLon/2) 					* sin($dLon/2);
			return 2 * $earthRadius * atan2(sqrt($a), sqrt(1-$a));
		}
		return null;
	}
}
?>
