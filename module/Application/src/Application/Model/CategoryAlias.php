<?php
namespace Application\Model;

class CategoryAlias extends AbstractModel implements CategoryAliasInterface
{
	private $id;
	private $alias;
	private $category_id;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (integer) $data['id'] : NULL; 
		$this->alias = isset($data['alias']) ? (string) $data['alias'] : NULL;
		$this->category_id = isset($data['category_id']) ? (string) $data['category_id'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAlias()
	{
		return (string) $this->alias;
	}

}
?>
