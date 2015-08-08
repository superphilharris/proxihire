<?php
namespace Application\Model;

class AssetRate extends AbstractModel implements AssetRateInterface
{
	private $price;
	private $duration;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
	}
	/**
	 * {@inheritdoc}
	 */
	public function setPrice(float $price)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPrice()
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDuration(int $duration)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDuration()
	{
	}

}
?>
