<?php
namespace Application\Model;

class CategoryAliases implements CategoryAliasesInterface
{
	public $obj;

	public function __construct()
	{
		$string=substr(file_get_contents('public/js/categories.js'),13,-2);
		$this->obj=json_decode($string);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get()
	{
		return $this->obj;
	}
	
	/**
	 * Gets the category that has an alias name
	 * @param string $aliasName
	 * @return NULL|Category 
	 */
	public function getCategoryNameForAliasName($aliasName){
		$ancestory = $this->getAncestoryRecursive($aliasName, $this->get());
		if($ancestory === null) return null;
		else					return $ancestory[0]->aliases[0]; 
	}

	/**
	 * Gets all the ancestors of a category name, orders them by youngest to oldest
	 * @param string $aliasName				- alias name to find in the big tree
	 * @return array(CategoryAliases)|NULL	- null if the cate
	 */
	public function getAncestoryForAliasName($aliasName){
		$ancestory = $this->getAncestoryRecursive($aliasName, $this->get());
		if ($ancestory === null) 	return array($this->get());
		else 						return $ancestory;
	}
	private function getAncestoryRecursive($aliasName, $alias){
		if(property_exists($alias, 'children')) {
			foreach($alias->children as $child){
				if(in_array($aliasName, $child->aliases)){
					return array($child, $alias);
				}
				$descendants = $this->getAncestoryRecursive($aliasName, $child);
				if($descendants !== null){
					array_push($descendants, $alias);
					return $descendants;
				}
			}
		}
		return null;
	}
}

?>
