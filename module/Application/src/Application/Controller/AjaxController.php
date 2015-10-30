<?php

namespace Application\Controller;

use Application\Service\AssetServiceInterface;
use Application\Service\CategoryServiceInterface;
use Application\Service\CategoryAliasesServiceInterface;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AjaxController extends AbstractActionController
{
	protected $assetService;
	protected $categoryService;
	protected $categoryAliasesService;

	public function __construct(AssetServiceInterface           $assetService,
	                            CategoryServiceInterface        $categoryService,
	                            CategoryAliasesServiceInterface $categoryAliasesService)
	{
		$this->assetService=$assetService;
		$this->categoryService=$categoryService;
		$this->categoryAliasesService=$categoryAliasesService;
	}

    public function assetListAction()
    {
		$view = new ViewModel(array());
		$this->layout( 'application/ajax' );
		$allCategoryAliases = $this->categoryAliasesService->getCategoryAliases();
		$categoryName       = $allCategoryAliases->getCategoryNameForAliasName($this->params()->fromRoute('category'));
		if($categoryName !== null){
			$category = $this->categoryService->getCategoryByName($categoryName);
		}else{
			$category = null;
		}
		$ancestory          = $allCategoryAliases->getAncestoryForAliasName($this->params()->fromRoute('category'));
		
		// Category picker
		$categoryPickerView = new ViewModel(array(
				'category'        => $category,
				'categoryAliases' => $allCategoryAliases,
				'ancestory'       => $ancestory
		));
		$categoryPickerView->setTemplate('application/search/category-picker');

		// Asset list
		$assetList = $this->getAssetList($category, $allCategoryAliases);
		$lessorList = $this->assetService->getLessorsForAssets( $assetList );
		$resultListView = new ViewModel(array(
			'assetList' => $assetList,
			'lessorList' => $lessorList
		));
		$resultListView->setTemplate('application/search/result-list');

		$view->setTemplate('application/search/index');
		$view->addChild($categoryPickerView, 'category_picker')
			 ->addChild($resultListView, 'result_list');
		
		return $view;
	}

	private function getAssetList($category, $allCategoryAliases)
	{
		// psh TODO: return the id's the category aliases and recursively get all the children aliases
		$filters  = json_decode(urldecode($_SERVER['QUERY_STRING']));
		$location = $this->params()->fromRoute('location');

		return $this->assetService->getAssetList($category,$filters,$location);
	}

	
}

?>
