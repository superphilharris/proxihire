<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\LessorInterface;

class LessorMapper extends AbstractMapper implements LessorMapperInterface
{
	/**
	 * @param AdapterInterface $dbAdapter
	 * @param AdapterInterface $hydrator
	 */
	public function __construct(
		$dbAdapter,
		$hydrator,
		$userPrototypeArray,
		$dbStructure
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\LessorInterface",
			"object"));
		
		parent::construct( $dbAdapter, $hydrator, $userPrototypeArray, $dbStructure );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrls($urlMapper,$reload=false)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Mapper\UrlMapperInterface",
			"boolean"
		));

		return $this->getSubObject($urlMapper,'url','getUrlId','getUrl','setUrl');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLessors($reload=false)
	{
		// jih: reload the users if $reload is set
		return $this->prototypeArray;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\LessorInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
