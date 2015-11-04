<?php

namespace Application\Controller;

use Application\Service\AssetServiceInterface;
use Application\Service\CategoryServiceInterface;
use Application\Service\CategoryAliasesServiceInterface;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SearchController extends AbstractActionController
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

    public function indexAction()
    {
		$view = new ViewModel(array());

		// Map
		$mapView = new ViewModel();
		$mapView->setTemplate('application/search/map');

		$view->addChild($this->getCategoryPickerView(), 'category_picker')
			 ->addChild($this->getAssetListView(), 'result_list')
			 ->addChild($mapView,        'map');

		return $view;
	}

	private function getAssetListView(){
		$allCategoryAliases = $this->categoryAliasesService->getCategoryAliases();
		$categoryName       = $allCategoryAliases->getCategoryNameForAliasName($this->params()->fromRoute('category'));
		if($categoryName !== null){
			$category = $this->categoryService->getCategoryByName($categoryName);
		}else{
			$category = null;
		}

		// Asset list
		$assetList = $this->getAssetList($category, $allCategoryAliases);
		$lessorList = $this->assetService->getLessorsForAssets( $assetList );
		$view = new ViewModel(array(
			'assetList' 	=> $assetList,
			'categoryName' 	=> $categoryName,
			'lessorList' 	=> $lessorList
		));
		$view->setTemplate('application/search/result-list');
		return $view;
	}

	private function getCategoryPickerView(){
		$allCategoryAliases = $this->categoryAliasesService->getCategoryAliases();
		$categoryName       = $allCategoryAliases->getCategoryNameForAliasName($this->params()->fromRoute('category'));
		if($categoryName !== null){
			$category = $this->categoryService->getCategoryByName($categoryName);
		}else{
			$category = null;
		}
		$ancestory          = $allCategoryAliases->getAncestoryForAliasName($this->params()->fromRoute('category'));
		
		// Category picker
		$view = new ViewModel(array(
				'category'        => $category,
				'categoryAliases' => $allCategoryAliases,
				'ancestory'       => $ancestory
		));
		$view->setTemplate('application/search/category-picker');
		return $view;
	}

	public function assetListAction()
	{
		$view = new ViewModel(array());
		$this->layout( 'application/ajax' );

		$view->addChild($this->getCategoryPickerView(), 'category_picker')
		     ->addChild($this->getAssetListView(), 'result_list');
		$view->setTemplate('application/search/index');
		
		return $view;
	}

	private function getAssetList($category, $allCategoryAliases)
	{
		$filters  = json_decode(urldecode($_SERVER['QUERY_STRING']));
		return $this->assetService->getAssetList($category,$filters);
	}

	
}

?>
