<?php
namespace Application\Model;

interface HydratableModelInterface
{
	/**
	 * Allows the model object to get populated by passing in a hashed array.
	 *
	 * @param $data Hashed array containing objects properties names => values
	 */
	public function exchangeArray($data);
}

?>
