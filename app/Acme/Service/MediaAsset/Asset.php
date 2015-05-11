<?php namespace Acme\Service\MediaAsset;


class Asset
{
	protected $filepath;
	protected $tempDir;
	protected $derivatives = [];

	/**
	 * Serialized representation of asset for JSON response
	 */
	public function toArray()
	{

	}

	public function getDerivatives()
	{
		return $this->derivatives;
	}

	public function addDerivative($name, array $data)
	{
		$this->derivatives[$name] = $data;
	}

	public function removeDerivative($name)
	{
		if (!isset($this->derivatives[$name]))
			return false;

		unset($this->derivatives[$name]);

		return true;
	}
} 
