<?php
namespace Application\Model;

interface CategoryAliasInterface
{
	/**
	 * Returns the category alias's id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Returns the category alias
	 *
	 * @return string
	 */
	public function getAlias();

}
?>
