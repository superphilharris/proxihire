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
		$this->id = isset($data['id']) ? (integer) $data['id'] : NULL; // jih: make sure that all models are typecasting
		$this->alias = isset($data['alias']) ? (string) $data['alias'] : NULL;
		$this->category_id = isset($data['category_id']) ? (string) $data['category_id'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId()
	{
		return (integer) $this->id; // jih: make sure that all models are typecasting
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
