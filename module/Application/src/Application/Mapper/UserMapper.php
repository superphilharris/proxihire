<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\UrlInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface;


class UrlMapper extends AbstractMapper implements UrlMapperInterface
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
			"array|Application\Model\UrlInterface",
			"object"));
		
		parent::construct( $dbAdapter, $hydrator, $userPrototypeArray, $dbStructure );
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterRetrieval()
	{
	}

	/**
	 * {@inheritdoc}
	 */
	// jih: assetmapper should be changed to getUrl as well.
	public function getUrl($urlMapper,$reload=false)
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
	public function getUsers($reload=false)
	{
		// jih: reload the users if $reload is set
		return $this->prototypeArray;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\UserInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
