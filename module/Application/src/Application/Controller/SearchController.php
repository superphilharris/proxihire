<?php

namespace Application\Controller;

use Application\Service\AssetServiceInterface;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SearchController extends AbstractActionController
{
	protected $assetService;

	public function __construct(AssetServiceInterface $assetService)
	{
		$this->assetService=$assetService;
	}

    public function indexAction()
    {
		$view = new ViewModel(array(
			'category'  => $this->params()->fromRoute('category'),
		));

		$resultListView = new ViewModel(array(
			'assetList' => $this->getAssetList(),
		));
		$resultListView->setTemplate('application/search/result-list');

		$mapView = new ViewModel();
		$mapView->setTemplate('application/search/map');

		$view->addChild($resultListView, 'result_list')
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
