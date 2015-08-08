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

		// Category picker
		$category = $this->categoryService->getCategoryByName($this->params()->fromRoute('category'));

		$categoryPickerView = new ViewModel(array(
			'category'  => $category,
			'categoryAliases' => $this->categoryAliasesService->getCategoryAliases()->get(),
		));
		$categoryPickerView->setTemplate('application/search/category-picker');

		// Asset list
		$resultListView = new ViewModel(array(
			'assetList' => $this->getAssetList($category),
		));
		$resultListView->setTemplate('application/search/result-list');

		// Map
		$mapView = new ViewModel();
		$mapView->setTemplate('application/search/map');

		$view->addChild($categoryPickerView, 'category_picker')
			 ->addChild($resultListView, 'result_list')
			 ->addChild($mapView,        'map');

		return $view;
	}

	private function getAssetList($category)
	{
		$filters  = json_decode(urldecode($_SERVER['QUERY_STRING']));
		$location = $this->params()->fromRoute('location');

		return $this->assetService->getAssetList($category,$filters,$location);
	}

}

?>
