<?php
namespace Application\Model;

interface CategoryAliasesInterface
{
	/**
	 * Gets the categoryAliases tree object
	 *
	 * @return object
	 */
	public function get();
	
	/**
	 * Gets the category that has an alias name
	 * @param string $aliasName
	 * @return NULL|String
	 */
	public function getCategoryNameForAliasName($aliasName);
}
?>
