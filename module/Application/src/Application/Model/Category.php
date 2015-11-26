<?php
namespace Application\Model;

class Category extends AbstractModel implements CategoryInterface
{
	protected $id;
	protected $name;
	protected $loads;
	protected $alias_array;
	protected $alias_id_array;
	protected $parent_id;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (integer) $data['id'] : NULL;
		$this->name = isset($data['name']) ? (string) $data['name'] : NULL;
		$this->loads = isset($data['loads']) ? (string) $data['loads'] : NULL;
		$this->parent_id = isset($data['parent_id']) ? (integer) $data['parent_id'] : NULL;
		$this->alias_id_array = isset($data['alias_id_array']) ? $data['alias_id_array'] : array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName(string $name)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAliases()
	{
		if( ! is_array($this->alias_array) ) $this->alias_array=array();
		return $this->alias_array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAliases($aliases)
	{
		// jih: classhelper
		$this->alias_array=$aliases;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAliasIds()
	{
		if( ! is_array($this->alias_id_array) ) $this->alias_id_array=array();
		foreach( $this->alias_id_array as $key => $alias_id ){
			$this->alias_id_array[$key] = (integer) $alias_id;
		}
		return $this->alias_id_array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setParent(CategoryInterface $category)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return $this->parent_id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getChildren()
	{
	}

	/**
	 * {@inheritdoc}
	 * jih: actual inheritdoc
	 */
	public function getLoads()
	{
		return $this->loads;
	}

	/**
	 * {@inheritdoc}
	 * jih: actual inheritdoc
	 */
	public function incrementLoads()
	{
		$this->loads++;
	}
}
?>
