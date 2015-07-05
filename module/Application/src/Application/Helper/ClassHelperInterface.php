<?php
namespace Application\Helper;

interface ClassHelperInterface
{
	/**
	 * Checks all of the arguments of the specified method
	 *
	 * @param string $methodName
	 * @param array  $arg
	 * @param array  $expectedType
	 */
	public static function checkAllArguments($methodName, $argArray, $expectedTypeArray);
}
?>
