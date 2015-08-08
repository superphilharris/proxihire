<?php
namespace Application\Model;

interface DatatypeInterface
{
	/**
	 * Returns the datatypes's id
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
