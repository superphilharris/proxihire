<?php
namespace Application\Helper;

interface ClassHelperInterface
{
	/**
	 * Checks all of the arguments of the specified method.
	 *
	 * Will throw an InvalidArgumentException if the arguments don't match that 
	 * specified by the `$expectedTypeArray`. This exception can then be caught 
	 * and re-thrown by the calling class.
	 *
	 * @param string $methodName        A string representation of the method 
	 *                                  name. Used only in the printing of the 
	 *                                  error message.
	 * @param array  $argArray          An array that contains all of the 
	 *                                  arguments. There are no constraints on 
	 *                                  the keys other than they must match that 
	 *                                  of the $expectedTypeArray.
	 * @param array  $expectedTypeArray An array that contains string 
	 *                                  representations of the types that each 
	 *                                  of the elements of `$argArray` should 
	 *                                  be. Case insensitive. There are no 
	 *                                  constraints on the keys except that they 
	 *                                  match that of the `$argArray`. When the 
	 *                                  corresponding item of the `$argArray` 
	 *                                  can be multiple types, the element can 
	 *                                  be separated by the pipe character. For 
	 *                                  example 'array|null'. When the argument 
	 *                                  can be any type, use the 'mixed' string.
	 */
	public static function checkAllArguments($methodName, $argArray, $expectedTypeArray);
}
?>
