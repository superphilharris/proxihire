<?php
namespace Application\Model;

class User extends AbstractModel implements UserInterface
{
	protected $id;
	protected $name;
	protected $branch_id_array;
	protected $branches;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (integer) $data['id'] : NULL;
		$this->name = isset($data['name']) ? (string) $data['name'] : NULL;
		$this->branch_id_array = isset($data['branch_id_array']) ? $data['branch_id_array'] : array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return (string) $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBranchIds()
	{
		if( !is_array( $this->branch_id_array ) ){
			$this->branch_id_array = array($this->branch_id_array);
		}
		foreach( $this->branch_id_array as $key => $id ){
			$this->branch_id_array[$key] = (integer) $id;
		}
		return $this->branch_id_array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBranches()
	{
		return $this->branches;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setBranches($branches)
	{
		// jih: classhelper
		$this->branches = $branches;
	}
}
?>
