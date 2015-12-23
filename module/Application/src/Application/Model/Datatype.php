<?php
namespace Application\Model;

use Application\Helper\ClassHelper;

class Datatype extends AbstractModel implements DatatypeInterface
{
	private $id;
	private $datatype;
	
	const ANGLE				= 'angle';
	const AREA				= 'area';
	const BOOLEAN			= 'boolean';
	const CURRENT			= 'current';
	const FLOW				= 'flow';
	const FORCE 			= 'force';
	const FREQUENCY 		= 'frequency';
	const LINEAL 			= 'lineal';
	const POWER_ELECTRICAL 	= 'power_electrical';
	const POWER_MECHANICAL	= 'power_mechanical';
	const PRESSURE 			= 'pressure';
	const SPEED 			= 'speed';
	const STRING 			= 'string';
	const TEMPERATURE 		= 'temperature';
	const TIME 				= 'time';
	const TORQUE			= 'torque';
	const VOLTAGE 			= 'voltage';
	const VOLUME 			= 'volume';
	const WEIGHT 			= 'weight';
	const WEIGHT_FLOW		= 'weight_flow';
	

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (int) $data['id'] : NULL; 
		$this->datatype = isset($data['datatype']) ? (string) $data['datatype'] : NULL;
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
			case $this::AREA:
				if($value < 1){
					return array(0.0001, 	'cm²');
				}else{
					return array(1, 		"m²");
				}
			case $this::BOOLEAN:
				if($value == "1") 		return array("yes", 	"");
				elseif($value == "0") 	return array("no", 	"");
			case $this::FLOW:
				return array(0.471947443, 'cfm');
				break;
			case $this::POWER_MECHANICAL:
				return array(1, "hp");
			case $this::PRESSURE:
				return array(6894.75729, 'psi');
				break;
			case $this::SPEED:
				return array(1, "m/s");
			case $this::TEMPERATURE:
				return array(1, "°C");
			case $this::STRING:
				return array(null, "");
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
			case $this::WEIGHT_FLOW:
				return array(1000/3600, "kg/hr");
				
			case $this::CURRENT:
				$base = "A";
				break;
			case $this::FORCE:
				$base = "N";
				break;
			case $this::FREQUENCY:
				$base = "Hz";
				break;
			case $this::LINEAL:
				$base = "m";
				break;
			case $this::POWER_ELECTRICAL:
				$base = "W";
				break;
			case $this::TORQUE:
				$base = "Nm";
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
	
	public static function getDisplayName($datatype){
		if($datatype === Datatype::POWER_ELECTRICAL OR $datatype === Datatype::POWER_MECHANICAL) return 'power';
		else return $datatype;
	}

}
?>
