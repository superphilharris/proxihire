<?php
namespace Application\Helper;

class ClassHelper implements ClassHelperInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function checkAllArguments($methodName, $argArray, $expectedTypeArray){
		ClassHelper::checkArgument(__METHOD__,$methodName,"string",1);
		ClassHelper::checkArgument(__METHOD__,$argArray,"array",2);
		ClassHelper::checkArgument(__METHOD__,$expectedTypeArray,"array",3);

		if( count($argArray) != count($expectedTypeArray) ){
			throw new \InvalidArgumentException("Arguments 2 and 3 of ".__METHOD__." must be of the same length.");
		}

		foreach ( $argArray as $key => $arg ){
			if ( !isset($expectedTypeArray[$key]) ){
				throw new \InvalidArgumentException("Arguments 2 and 3 of ".__METHOD__." must use the same keys.");
			}

			ClassHelper::checkArgument($methodName,$arg,$expectedTypeArray[$key],$key);
		}
	}

	/**
	 * Checks the argument at the specified index of the specified method.
	 *
	 * @param string $methodName
	 * @param mixed  $arg
	 * @param string $expectedType
	 * @param int    $index
	 */
	private static function checkArgument($methodName, $arg, $expectedType, 
		$index=1){
		$actType=strtolower(gettype($methodName));
		if ( $actType != "string" ){
			throw new \InvalidArgumentException("Argument number 1 of the ".
				 "`ClassHelper::checkAllArguments` method should be an instance of string. ".$actType." given.");
		}
		$actType=strtolower(gettype($expectedType));
		if( $actType != "string" ){
			throw new \InvalidArgumentException("Argument number 3 of the ".
				"`ClassHelper::checkAllArguments` method should be an instance of string. ".$actType." given.");
		}
		$actType=strtolower(gettype($index));
		if( $actType != "integer" ){
			throw new \InvalidArgumentException("Argument number 4 of the ".
				"`ClassHelper::checkAllArguments` method should be an instance of integer. ".$actType." given.");
		}

		$typeArray=explode("|",$expectedType);
		$typeMatch=false;
		$actType=strtolower(gettype($arg));
		foreach ( $typeArray as $type ){
			if ( $actType==$type || $type=="mixed" ){
				  $typeMatch=true;
			 }
		}

		if ( ! $typeMatch ){
			 throw new \InvalidArgumentException("Argument number ".$index." of the `".$methodName."` method should be an instance of ".$expectedType.". ".$actType." given.");
		}

	}

}
?>
