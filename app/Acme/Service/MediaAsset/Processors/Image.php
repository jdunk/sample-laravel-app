<?php namespace Acme\Service\MediaAsset\Processors;

use \Acme\Service\MediaAsset\Asset;
use \Acme\Service\MediaAsset\Processor;

use \Image as Intervention;

class Image extends Processor
{
	/**
	 * @var Asset
	 */
	protected $asset;

	protected $derivativeSizes = [
		'large' => ['w' => 1500, 'h' => 1500, 'q' => 92],
		'medium' => ['w' => 750, 'h' => 750, 'q' => 85],
		'small' => ['w' => 375, 'h' => 375, 'q' => 78]
	];

	/**
	 * @param array $data
	 * @return Asset
	 */
	public function process($file) {
		$this->asset = new Asset();

		$this->validateFileExists($file);

		$this->createDerivatives($file);

		return $this->asset;
	}

	public function createDerivatives($file)
	{

		foreach ($this->derivativeSizes as $name => $size) {
			$derivative = [
				'name' => $name,
				'file' => $this->workspaceFile($name . '.jpg')
			];

			$img = Intervention::make($file);

			// doesn't treat portrait and landscape equally
			//$img->fit($size['w'], $size['h']);

			$landscape = $img->width() > $img->height();

			$img->resize(
				$landscape ? $size['w'] : null,
				$landscape ? null : $size['h'],
				function ($constraint)
				{
					$constraint->aspectRatio();
					$constraint->upsize();
			});

			$img->save($derivative['file'], $size['q']);

			$this->asset->addDerivative($name, array_replace_recursive($derivative, [
				'filesize' => filesize($derivative['file']),
				'width' => $img->width(),
				'height' => $img->height(),
				'md5' => md5_file($derivative['file'])
			]));
		}

	}
} 
