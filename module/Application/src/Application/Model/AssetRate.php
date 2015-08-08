<?php
namespace Application\Model;

class AssetRate extends AbstractModel implements AssetRateInterface
{
	private $id;
	private $asset_id;
	private $price;
	private $duration;

	/**
	 * {@inheritdoc}
	 */
	public function exchangeArray($data)
	{
		$this->id = isset($data['id']) ? (int) $data['id'] : NULL;
		$this->asset_id = isset($data['asset_id']) ? (int) $data['asset_id'] : NULL;
		$this->price = isset($data['price']) ? (int) $data['price'] : NULL;
		$this->duration = isset($data['duration']) ? (int) $data['duration'] : NULL;
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
		return (integer) $this->price;
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
		return (integer) $this->duration;
	}

}
?>
