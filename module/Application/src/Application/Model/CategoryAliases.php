<?php
namespace Application\Model;

class CategoryAliases implements CategoryAliasesInterface
{
	public $obj;

	public function __construct()
	{
		$string=substr(file_get_contents('public/js/categories.js'),13,-2);
		$this->obj=json_decode($string);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get()
	{
		return $this->obj;
	}
}

?>
