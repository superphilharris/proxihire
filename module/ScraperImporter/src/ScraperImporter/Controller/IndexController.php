<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ScraperImporter\Controller;

use ScraperImporter\Service\ImporterServiceInterface;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	const DATADIR='module/ScraperImporter/data';
	protected $importerService;

	/**
	 * Class constructor
	 *
	 * @param ScraperImporter\Service\ImporterServiceInterface
	 */
	public function __construct(
		ImporterServiceInterface $importerService
	){
		$this->importerService = $importerService;
	}

	public function indexAction()
	{
		$this->layout( 'scraper-importer/layout' );
		$view=new ViewModel(array(
			'files' => preg_grep( "/^[a-z_]*\.json$/", scandir( self::DATADIR ) )
		));
		return $view;
	}

	public function viewAction()
	{
		$this->layout( 'scraper-importer/layout' );
		$view=new ViewModel(array(
			'assets' => $this->importerService->getAssets( $this->getJsonArray() ),
			'file'   => $this->params()->fromRoute( 'file' )
		));
		return $view;
	}

	public function dumpAction()
	{
		$this->layout( 'scraper-importer/layout' );
		$view=new ViewModel(array(
			'assets' => $this->importerService->dumpAssets( $this->getJsonArray() ),
			'file'   => $this->params()->fromRoute( 'file' )
		));
		return $view;
	}

	private function getJsonArray(){
		$file        = $this->params()->fromRoute( 'file' );
		$filePath    = self::DATADIR."/$file";
		$fileContent = file_get_contents($filePath);
		if(!$fileContent) throw new \Exception("The file path does not exist: $filePath");
		$items       = json_decode($fileContent);
		if($items === null){
			throw new \Exception("The $filePath is not valid json. Did you kill a scrape, and not delete the output file properly? Error message: " . json_last_error_msg());
		}
		return (array) $items;
	}
}

?>
