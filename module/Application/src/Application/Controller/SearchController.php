<?php

namespace Application\Controller;

use Application\Service\AssetServiceInterface;
use Application\Service\CategoryAliasesServiceInterface;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SearchController extends AbstractActionController
{
	protected $assetService;

	public function __construct(AssetServiceInterface           $assetService,
	                            CategoryAliasesServiceInterface $categoryAliasesService)
	{
		$this->assetService=$assetService;
		$this->categoryAliasesService=$categoryAliasesService;
	}

    public function indexAction()
    {
		$view = new ViewModel(array());

		$categoryPickerView = new ViewModel(array(
			'category'  => $this->params()->fromRoute('category'),
			'categoryAliases' => $this->categoryAliasesService->getCategoryAliases()->get(),
		));
		$categoryPickerView->setTemplate('application/search/category-picker');

		$resultListView = new ViewModel(array(
			'assetList' => $this->getAssetList(),
		));
		$resultListView->setTemplate('application/search/result-list');

		$mapView = new ViewModel();
		$mapView->setTemplate('application/search/map');

		$view->addChild($categoryPickerView, 'category_picker')
			 ->addChild($resultListView, 'result_list')
			 ->addChild($mapView,        'map');

        return $view;
    }

	private function getAssetList()
	{
		$filters  = json_decode(urldecode($_SERVER['QUERY_STRING']));
		$category = $this->params()->fromRoute('category');
		$location = $this->params()->fromRoute('location');
		// TODO: do actual retrieval of asset list

		return $this->assetService->getAssetsInCategory($category,$filters);
	}

}

?>
