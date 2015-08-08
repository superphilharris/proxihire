<?php
namespace Application\Model;

class Datatype extends AbstractModel implements DatatypeInterface
{
	private $id;
	private $datatype;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (int) $data['id'] : NULL; // jih: make sure that all models are typecasting
		$this->datatype = isset($data['datatype']) ? (string) $data['datatype'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId()
	{
		return (integer) $this->id; // jih: make sure that all models are typecasting
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDatatype()
	{
		return (string) $this->datatype;
	}

}
?>
