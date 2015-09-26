<?php
namespace Application\Model;

class Datatype extends AbstractModel implements DatatypeInterface
{
	private $id;
	private $datatype;
	
	const ANGLE		= 'angle';
	const FLOW		= 'flow';
	const FORCE 	= 'force';
	const FREQUENCY = 'frequency';
	const LINEAL 	= 'lineal';
	const POWER 	= 'power';
	const PRESSURE 	= 'pressure';
	const SPEED 	= 'speed';
	const TIME 		= 'time';
	const VOLTAGE 	= 'voltage';
	const VOLUME 	= 'volume';
	const WEIGHT 	= 'weight';
	

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (int) $data['id'] : NULL; // jih: make sure that all models are typecasting
		$this->datatype = isset($data['datatype']) ? (string) $data['datatype'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId()
	{
		return (integer) $this->id; // jih: make sure that all models are typecasting
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDatatype()
	{
		return (string) $this->datatype;
	}
	
	public function getUnit($value){
		$base = null;
		switch ($this->datatype) {
			case $this::ANGLE:
				return 		array(1, 	"°");
			case $this::TIME:
				if($value < 1) {
					return 	array(0.001, 	"ms");
				}elseif($value < 120){
					return 	array(1, 		"s");
				}elseif($value < 3600){
					return 	array(60,		"min");
				}elseif($value < 172800){
					return 	array(3600, 	"hours");
				}elseif($value < 2592000){
					return 	array(86400,	"days");
				}else{
					return 	array(2.62974*10^6,	"months");
				}
				return "";
				
			case $this::POWER:
				return ""; // TODO: psh
				
			case $this::FORCE:
				$base = "N";
				break;
			case $this::FREQUENCY:
				$base = "Hz";
				break;
			case $this::LINEAL:
				$base = "m";
				break;
			case $this::VOLTAGE:
				$base = "V";
				break;
			case $this::VOLUME:
				$base = "L";
				break;
			case $this::WEIGHT:
				$base = "g";
				break;
				
			default:
				throw new \Exception("There are no units defined for the $this->datatype datatype. Please add one.");
		}
		// Metric Units
		if($base !== null){
			if($value < 0.001){
				return 	array(10^-6, "μ" + $base);
			}elseif($value < 1){
				return 	array(10^-3, "m" + $base);
			}elseif($value < 1){
				return 	array(10^0,  ""  + $base);
			}elseif($value < 1){
				return 	array(10^3,  "k" + $base);
			}elseif($value < 1){
				return 	array(10^6,  "M" + $base);
			}elseif($value < 1){
				return 	array(10^9,  "G" + $base);
			}elseif($value < 1){
				return 	array(10^12, "T" + $base);
			}else{
				return "";
			}
		}
		return "";
	}

}
?>
