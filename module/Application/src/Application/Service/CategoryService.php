<?php
namespace Application\Service;

use Application\Helper\ClassHelper;
use Application\Mapper\CategoryMapperInterface;

class CategoryService implements CategoryServiceInterface
{
	protected $categoryMapper;

	public function __construct( CategoryMapperInterface $categoryMapper )
	{
		$this->categoryMapper = $categoryMapper;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCategoryByName( $categoryName )
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("string"));

		return $this->categoryMapper->findByName( $categoryName );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCategory($id)
	{
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("integer"));

		return $this->categoryMapper->find($id);
	}
}
?>
