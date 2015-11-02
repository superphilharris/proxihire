<?php
namespace Application\Service;

use Application\Helper\ClassHelper;
use Application\Model\GeonameInterface;
use Application\Mapper\GeonameMapperInterface;

class GeonameService implements GeonameServiceInterface
{
	protected $geonameMapper;
	protected $geonamePrototype;

	public function __construct( 
		GeonameInterface $geonamePrototype,
		GeonameMapperInterface $geonameMapper
	){
		$this->geonamePrototype = $geonamePrototype;
		$this->geonameMapper = $geonameMapper;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getGeonamesLike(
		$name,
		$number=15
	){
		$name = str_replace(' ', '%', trim($name)) . '%';
		$name = str_replace('%%', '%', $name);
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("string"));

		$geonameArray=array();
		for( $i=0; $i<$number; $i++){
			$geonameArray[$i]=new $this->geonamePrototype;
			$properties = array();
		}

		$this->geonameMapper->setPrototypeArray($geonameArray);
		$this->geonameMapper->findLike( $name, $number );

		return $this->geonameMapper->getPrototypeArray();
	}
}
?>
