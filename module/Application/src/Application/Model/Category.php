<?php
namespace Application\Model;

class Category extends AbstractModel implements CategoryInterface
{
	protected $id;
	protected $name;
	protected $alias_array;
	protected $parent_id;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? $data['id'] : NULL;
		$this->name = isset($data['name']) ? $data['name'] : NULL;
		$this->parent_id = isset($data['parent_id']) ? $data['parent_id'] : NULL;

		if( ! empty($data['aliases']) ){
			foreach( $data['aliases'] as $alias ){
				$this->alias_array[]=$alias;
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId()
	{
		return $this->id;
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
		return $this->alias_array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addAlias(string $alias)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteAlias(string $alias)
	{
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
}
?>
