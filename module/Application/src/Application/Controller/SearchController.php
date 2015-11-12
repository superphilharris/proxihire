<?php

namespace Application\Controller;

use Application\Service\AssetServiceInterface;
use Application\Service\CategoryServiceInterface;
use Application\Service\CategoryAliasesServiceInterface;
use Application\Model\Category;
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
		$view = $this->getAssetListAndCategoryPickerView();

		// Map
		$mapView = new ViewModel();
		$mapView->setTemplate('application/search/map');
		$view->addChild($mapView, 'map');

		return $view;
	}

	private function getAssetListView($category, $categoryName, $allCategoryAliases){
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

	private function getCategoryPickerView($category, $allCategoryAliases){
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

	private function getAssetListAndCategoryPickerView(){
                $allCategoryAliases = $this->categoryAliasesService->getCategoryAliases();
                $categoryName       = $allCategoryAliases->getCategoryNameForAliasName($this->params()->fromRoute('category'));
                if($categoryName !== null){
                        $category = $this->categoryService->getCategoryByName($categoryName);
                }else{
                        $category = new Category();
                        $category->exchangeArray(array($allCategoryAliases->get()));
                }

		$view = new ViewModel(array());
                $view->addChild($this->getCategoryPickerView($category, $allCategoryAliases), 		'category_picker')
                     ->addChild($this->getAssetListView($category, $categoryName, $allCategoryAliases), 'result_list');
		return $view;
	}
	public function assetListAction()
	{
		$this->layout( 'application/ajax' );

		$view = $this->getAssetListAndCategoryPickerView();
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
