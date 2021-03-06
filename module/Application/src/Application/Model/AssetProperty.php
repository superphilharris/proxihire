<?php
namespace Application\Model;

use Application\Helper\ClassHelper;

class AssetProperty extends AbstractModel implements AssetPropertyInterface
{
	private $id;
	private $name;
	private $datatype;
	private $datatype_id;
	private $asset;
	private $asset_id;
	private $value;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (integer) $data['id'] : NULL;
		$this->name = isset($data['name']) ? (string) $data['name'] : NULL;
		$this->datatype_id = isset($data['datatype_id']) ? (integer) $data['datatype_id'] : NULL;
		$this->asset_id = isset($data['asset_id']) ? (integer) $data['asset_id'] : NULL;
		$this->value = isset($data['value']) ? $data['value'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setId($id)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("integer"));
		$this->id=$id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDatatypeId()
	{
		return $this->datatype_id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDatatype($datatype)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("Application\Model\DatatypeInterface"));
		$this->datatype=$datatype;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDatatype()
	{
		return $this->datatype;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAsset($asset)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("Application\Model\AssetInterface"));
		$this->asset=$asset;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAsset()
	{
		return $this->asset;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName($name)
	{
		$this->name=$name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPropertyType($type)
	{
		$this->datatype=$type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPropertyType()
	{
		return $this->datatype;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Gets the value and unit for the asset property
	 * @param string $referenceValue	- number that we can use as a reference
	 * so that we are converting to a standard unit. (typically the average in a list)
	 * @return array(string|float, string)	- the value and string
	 */
	public function getValueAndUnit($referenceValue=null){
		$referenceValue = ($referenceValue===null)? $this->value : $referenceValue;
		if(is_numeric($referenceValue) AND is_numeric($this->value)) {
			list($factor, $unit) = $this->datatype->getFactorAndUnit($referenceValue);
			if($unit !== ""){
				return array($this->roundToSF($this->value/$factor, 2), $unit);
			}elseif($factor == "yes" OR $factor == "no"){
				return array($factor, "");
			}
		}
		return array($this->value, "");
	}
	
	private function roundToSF($number, $sf){
		$number = floatval($number);
		if($number == 0.0) return 0;
		$digits = (int)(log10($number));
		if($digits < -1000) return 0;
		return (pow(10, $digits)) * round($number/(pow(10, $digits)), $sf-1);
	}
	
}
?>
