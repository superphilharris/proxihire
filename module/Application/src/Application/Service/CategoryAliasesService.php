<?php

namespace Application\Service;

use Application\Model\CategoryAliases;

class CategoryAliasesService implements CategoryAliasesServiceInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getCategoryAliases()
	{
		$categoryAliases = new CategoryAliases();
		return $categoryAliases;
	}
}
?>
