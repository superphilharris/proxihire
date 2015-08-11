<?php
namespace Application\Model;

interface DatatypeInterface
{
	/**
	 * Returns the datatype's id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Returns the datatype
	 *
	 * @return string
	 */
	public function getDatatype();

}
?>
