<?php

namespace Application\Controller;

use Application\Service\AssetServiceInterface;
use Application\Service\CategoryServiceInterface;
use Application\Service\CategoryAliasesServiceInterface;
use Application\Service\GeonameServiceInterface;
use Application\Model\Category;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SearchController extends AbstractActionController
{
	protected $assetService;
	protected $categoryService;
	protected $categoryAliasesService;

	public function __construct(
	            AssetServiceInterface           $assetService,
	            CategoryServiceInterface        $categoryService,
	            CategoryAliasesServiceInterface $categoryAliasesService,
	            GeonameServiceInterface         $geonameService)
	{
		$this->assetService=$assetService;
		$this->categoryService=$categoryService;
		$this->categoryAliasesService=$categoryAliasesService;
		$this->geonameService=$geonameService;
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
			'assetList'    => $assetList,
			'categoryName' => $categoryName,
			'lessorList'   => $lessorList,
			'filters'      => $this->getFilters()
		));
		$view->setTemplate('application/search/result-list');
		return $view;
	}

	private function getCategoryPickerView($category, $allCategoryAliases){
		$ancestry          = $allCategoryAliases->getAncestryForAliasName($this->params()->fromRoute('category'));
		$filters = $this->getFilters();
		if( isset($filters->location->latitude->user) &&
		    isset($filters->location->longitude->user) ){
			$locations=$this->geonameService->getClosestLocation( $filters->location->latitude->user, $filters->location->longitude->user);
			$location=count($locations)>0?$locations[0]:NULL;
		}else{
			$location=NULL;
		}
		
		// Category picker
		$view = new ViewModel(array(
				'category'        => $category,
				'categoryAliases' => $allCategoryAliases,
				'ancestory'       => $ancestry,
				'location'        => $location
		));
		$view->setTemplate('application/search/category-picker');
		return $view;
	}

	private function getAssetListAndCategoryPickerView(){
		$allCategoryAliases = $this->categoryAliasesService->getCategoryAliases();
		$categoryName       = $allCategoryAliases->getCategoryNameForAliasName($this->params()->fromRoute('category'));
		if($categoryName !== null){
			$category = $this->categoryService->getCategoryByName($categoryName);
			if( count($category)>0 ){
				$category=$category[0];
			}else{
				$category = new Category();
				$category->exchangeArray(array($allCategoryAliases->get()));
			}
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
		$assets   = $this->assetService->getAssetList($category, $allCategoryAliases, $filters);
		return $assets;
	}

	private function getFilters(){
		$filters  = json_decode(urldecode($_SERVER['QUERY_STRING']));
		if($filters == null) $filters = new \stdClass();

		# Convert location->radius to 
		# - location->latitude->min/max
		# - location->longitude->min/max
		if(! isset($filters->location) ){
			$filters->location = new \stdClass();
		} 
		if(! isset($filters->location->latitude) OR  ! isset($filters->location->longitude)){
			$filters->location->latitude  = new \stdClass();
			$filters->location->longitude = new \stdClass();
		}
		if(! isset($filters->location->latitude->user) OR ! isset($filters->location->longitude->user)){
			$filters->location->latitude->user = -36.84913134182603;
			$filters->location->longitude->user = 174.76234048604965;
		}
		
		// Set default min and max
		if( ! isset($filters->location->latitude->min) OR
			! isset($filters->location->latitude->max) OR
			! isset($filters->location->longitude->min) OR
			! isset($filters->location->longitude->max)
		){
			$range = isset($filters->location->radius) ? $filters->location->radius : 30; // In km ??
			$lat_radius=$range/110.574; # Convert km to \delta latitude
			$long_radius=$range/111.320*acos(deg2rad($filters->location->latitude->user)); # Convert km to \delta latitude
			$filters->location->latitude->min 	= $filters->location->latitude->user - $lat_radius;
			$filters->location->latitude->max 	= $filters->location->latitude->user + $lat_radius;
			$filters->location->longitude->min 	= $filters->location->longitude->user - $long_radius;
			$filters->location->longitude->max	= $filters->location->longitude->user + $long_radius;
		}

		return $filters;
	}

}

?>
