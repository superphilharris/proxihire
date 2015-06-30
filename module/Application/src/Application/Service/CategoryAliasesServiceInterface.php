<?php

namespace Application\Service;

use Application\Model\CategoryAliasesInterface;

interface CategoryAliasesServiceInterface
{
	/**
	 * Returns the tree object containg all category aliases
	 *
	 * @return CategoryAliasesInterface
	 */
	public function getCategoryAliases();
}
?>
