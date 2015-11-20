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
		$filters  = $this->getFilters();
		$assets   = $this->assetService->getAssetList($category);
		$this->assetService->filterAssets( $assets, $filters );
		return $assets;
	}

	private function getFilters(){
		$filters  = json_decode(urldecode($_SERVER['QUERY_STRING']));

		# Convert location->radius to 
		# - location->latitude->min/max
		# - location->longitude->min/max
		if( isset($filters->location) &&
		    isset($filters->location->latitude) &&
		    isset($filters->location->longitude) &&
		    isset($filters->location->radius)
		){
			$lat_radius=$filters->location->radius/110.574; # Convert km to \delta latitude
			$long_radius=$filters->location->radius/(111.320*cos($filters->location->latitude*pi()/180)); # Convert km to \delta latitude
			$location=(object) array(
				"latitude" =>(object) array(
					"min" => $filters->location->latitude - $lat_radius,
					"max" => $filters->location->latitude + $lat_radius),
				"longitude" =>(object) array(
					"min" => $filters->location->longitude - $long_radius,
					"max" => $filters->location->longitude + $long_radius));
			$filters->location=$location;
		}
		return $filters;
	}

}

?>
