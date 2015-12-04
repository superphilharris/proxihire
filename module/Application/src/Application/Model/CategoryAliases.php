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
		$ancestry = $this->getAncestryRecursive($aliasName, $this->get());
		if($ancestry === null) return null;
		else                   return $ancestry[0]->aliases[0]; 
	}

	/**
	 * Gets all the ancestors of a category name, orders them by youngest to oldest
	 * @param string $aliasName               - alias name to find in the big tree
	 * @return array(CategoryAliases)|NULL    - null if the cate
	 */
	public function getAncestryForAliasName($aliasName){
		$ancestry = $this->getAncestryRecursive($aliasName, $this->get());
		if ($ancestry === null) return array($this->get());
		else                    return $ancestry;
	}
	private function getAncestryRecursive($aliasName, $alias){
		if(property_exists($alias, 'children')) {
			foreach($alias->children as $child){
				foreach($child->aliases as $childAlias){
					if(strtolower($aliasName) == strtolower($childAlias)){
						return array($child, $alias);
					}
				}
				$descendants = $this->getAncestryRecursive($aliasName, $child);
				if($descendants !== null){
					array_push($descendants, $alias);
					return $descendants;
				}
			}
		}
		return null;
	}
	
	public function __toString(){
		return json_encode($this->obj);
	}

	/**
	 * Gets the children of the specified category.
	 *
	 * This method will recursively loop through the categoryAliases structure, 
	 * and find the specified alias. It will then return the first alias of each 
	 * of the children nodes. If `null` is passed as an argument, then it will 
	 * return the first alias of all of the root nodes.
	 *
	 * @param $aliasName - The name of the alias of which to get the children of
	 * @return array|string - An array of the first alias for each child node
	 */
	public function getChildrenOf( $aliasName=null ){
		$aliasStructure=$this->get();
		return $this->getChildrenOfRecursive( $aliasName, $aliasStructure );
	}

	private function getChildrenOfRecursive( $aliasName, $aliasStructure ){
		// If the aliasStructure object doesn't have an 'aliases' array, it is 
		// formatted incorrectly. Return null.
		if( ! isset(    $aliasStructure->aliases ) OR
		    ! is_array( $aliasStructure->aliases ) ){
			return null;
		}

		// If this is the node that we are looking for, set $aliasName to null. 
		// This will trigger returning the children of the current node.
		foreach( $aliasStructure->aliases as $alias ){
			if( $alias==$aliasName ){
				$aliasName=null;
			}
		}

		// If the current node has no children, then its children won't match the 
		// node that we are looking for. Return null.
		if( ! isset(    $aliasStructure->children ) OR
		    ! is_array( $aliasStructure->children ) ){
			return null;
		}

		// If the aliasName is 
		//
		// 1. Passed in as null
		// 2. Matches the current node
		//
		// Then return the children of this alias.
		if( is_null($aliasName) ){
			$childNodes=array();
			foreach( $aliasStructure->children as $child ){
				if( isset(    $child->aliases ) AND
				    is_array( $child->aliases ) ){
					array_push( $childNodes, $child->aliases[0] );
				}
			}
			return $childNodes;
		}

		// Recursively search all of the children for the node of interest. If it 
		// is found return it. Otherwise keep searching.
		foreach( $aliasStructure->children as $child ){
			$childNodes = $this->getChildrenOfRecursive( $aliasName, $child );
			if( ! is_null($childNodes) ){
				return $childNodes;
			}
		}
		return null;
	}

	public function getLeafNodesFor( $aliasName=null ){
		$leafNodes = $this->getLeafNodesRecursive( $aliasName, $this->get() );
		if( empty($leafNodes) ){
			return array( $aliasName );
		}
		return $leafNodes;
	}

	private function getLeafNodesRecursive( $aliasName, $aliasStructure ){
		if(property_exists($aliasStructure, 'children')) {
			$children=array();
			foreach($aliasStructure->children as $child){
				if( is_null($aliasName) ){
					$children=array_merge( $children, $this->getLeafNodesRecursive(null,$child) );
				}else{
					$found=false;
					foreach($child->aliases as $childAlias){
						if(strtolower($aliasName) == strtolower($childAlias)){
							return $this->getLeafNodesRecursive(null, $child);
						}
					}
					array_merge( $children, $this->getLeafNodesRecursive($aliasName, $child) );
				}
			}
			return $children;
		}elseif( is_null($aliasName) ){
			return array($aliasStructure);
		}
		return array();
	}
}

?>
