<?php

namespace Application\Controller;

use Application\Service\GeonameServiceInterface;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GeonameController extends AbstractActionController
{
	protected $geonameService;

	public function __construct(GeonameServiceInterface $geonameService)
	{
		$this->geonameService=$geonameService;
	}

    public function locationAction()
    {
		$location = $this->params()->fromRoute('location');
		$geolocations = $this->geonameService->getGeonamesLike($location);

		$view = new ViewModel(array(
			'geolocations' => $geolocations
		));
		$this->layout( 'application/ajax' );

		$view->setTemplate('application/json/geoname');
		return $view;
	}
}

?>
