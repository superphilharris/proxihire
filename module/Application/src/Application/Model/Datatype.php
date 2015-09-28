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
	const STRING 	= 'string';
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
	
	/**
	 * This gets the appropriate unit and factor for a value for this datatype
	 * @param float $value
	 * @throws \Exception				- if there is a datatype that we cannot convert
	 * @return array(number, string)	- returns the factor and unit
	 */
	public function getFactorAndUnit($value){
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
					return 	array(2.62974*pow(10, 6),	"months");
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
				if($value > pow(10, 6)) return array(pow(10, 6), 'ton');
				$base = "g";
				break;
			case $this::STRING:
				return array(null, "");
			default:
				throw new \Exception("There are no units defined for the $this->datatype datatype. Please add one.");
		}
		// Metric Units
		if($base !== null){
			$value = floatval($value);
			if(		$value <  pow(10, -3)){
				return 	array(pow(10, -6), "μ" . $base);
			}elseif($value <  pow(10, 0)){
				return 	array(pow(10, -3), "m" . $base);
			}elseif($value <  pow(10, 3)){
				return 	array(pow(10, 0),  ""  . $base);
			}elseif($value <  pow(10, 6)){
				return 	array(pow(10, 3),  "k" . $base);
			}elseif($value <  pow(10, 9)){
				return 	array(pow(10, 6),  "M" . $base);
			}elseif($value <  pow(10, 12)){
				return 	array(pow(10, 9),  "G" . $base);
			}elseif($value <  pow(10, 15)){
				return 	array(pow(10, 12), "T" . $base);
			}else{
				return array(1, "");
			}
		}
		return array(1, "");
	}

}
?>
