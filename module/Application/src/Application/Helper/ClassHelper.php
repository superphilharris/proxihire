<?php
namespace Application\Helper;

class ClassHelper implements ClassHelperInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function checkAllArguments($methodName, $argArray, $expectedTypeArray, $isArray=false ){
		// jih: if (ini_get(error_reporting) == none){ // if we are in production
		// jih:    return
		// jih: }

		ClassHelper::checkArgument(__METHOD__,$methodName,"string",1);
		ClassHelper::checkArgument(__METHOD__,$argArray,"array",2);
		ClassHelper::checkArgument(__METHOD__,$expectedTypeArray,"array",3);

		if( count($argArray) > count($expectedTypeArray) ){
			throw new \InvalidArgumentException("In ".$methodName.", the call to ".__METHOD__." must include a type for every argument.");
		}

		foreach ( $argArray as $key => $arg ){
			if ( !isset($expectedTypeArray[$key]) ){
				throw new \InvalidArgumentException("Arguments 2 and 3 of ".__METHOD__." must use the same keys.");
			}

			ClassHelper::checkArgument($methodName,$arg,$expectedTypeArray[$key],$key,$isArray);
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
		$index=1,$isArray=false){
		$actType=strtolower(gettype($methodName));
		if ( $actType != "string" ){
			throw new \InvalidArgumentException("Argument number 1 of the ".
				"`ClassHelper::checkAllArguments` method should be an instance of string. ".
				$actType." given.");
		}
		$actType=strtolower(gettype($expectedType));
		if( $actType != "string" ){
			throw new \InvalidArgumentException("Argument number 3 of the ".
				"`ClassHelper::checkAllArguments` method should be an instance of string. ".
				$actType." given.");
		}
		$actType=strtolower(gettype($index));
		if( $actType != "integer" ){
			throw new \InvalidArgumentException("Argument number 4 of the ".
				"`ClassHelper::checkAllArguments` method should be an instance of integer. ".
				$actType." given.");
		}

		
		$typeArray=explode("|",$expectedType);
		$typeMatch=false;
		$actType=strtolower(gettype($arg));

		// If the expected type for this item is 'efg|array|abc', and the actual type 
		// is an array, then ensure that it is an array of 'efg|abc' type elements.
		//
		// If the expected type is simply 'array', then it is allowed to be an 
		// array of anything. Therefore, don't check the elements.
		//
		// If the array is empty, don't check any of its elements.
		if ( $actType == 'array' AND $expectedType != 'array' AND count($arg)>0 ){
			$expectedElementType="";
			foreach ( $typeArray as $type ){
				if ( $type != 'array' ){
					$expectedElementType = ($expectedElementType=="") ? $type : $expectedElementType . '|' . $type;
				}
			}
			$expectedElementTypeArray=array();
			foreach ( $arg as $key=>$argElement ){
				$expectedElementTypeArray[$key]=$expectedElementType;
			}
			self::checkAllArguments($methodName,$arg,$expectedElementTypeArray,true);
		}

		foreach ( $typeArray as $type ){
			// jih: if ( ! is_type( $type ) and `jih: 2:`;
			if ( $actType==$type || $type=="mixed" ){ 
				$typeMatch=true;
			} elseif ( ! $typeMatch ) {
				$interfaceArray=explode("&",$type);
				$typeMatch = true;
				foreach ( $interfaceArray as $interface ){
					// jih: 2: if ( ! is_interface( $interface )) error;
					if ( ! $arg instanceof $interface ){
						$typeMatch=false;
					}
				}
			}
		}

		if ( ! $typeMatch ){
			if( $isArray ){
				$errorString="Argument number ".$index." of the `".$methodName."` method should be an array of ".$expectedType.". An array with element ".$actType." given";
			}else{
				$errorString="Argument number ".$index." of the `".$methodName."` method should be an instance of ".$expectedType.". ".$actType." given";
			}

			if( $actType=="object" ) {
				$errorString=$errorString." which implements ".print_r( class_implements($arg), true );
			}else{
				$errorString=$errorString.".";
			}
			$errorString=$errorString."\narg = ".var_export($arg,true);
			 throw new \InvalidArgumentException( $errorString );
		}

	}

}
?>
