<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\UrlInterface;

class UrlMapper extends AbstractMapper implements UrlMapperInterface
{
	/**
	 * @param Zend\Db\Adapter\AdapterInterface $dbAdapter
	 * @param Zend\Stdlib\HydratorInterface $hydrator
	 * @param AdapterInterface $urlPrototypeArray
	 */
	public function __construct(
		$dbAdapter,
		$hydrator,
		$urlPrototypeArray,
		$dbStructure
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\UrlInterface",
			"object"));

		parent::construct( $dbAdapter, $hydrator, $urlPrototypeArray, $dbStructure );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\UrlInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
