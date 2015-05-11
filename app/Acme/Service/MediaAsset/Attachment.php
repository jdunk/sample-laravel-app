<?php namespace Acme\Service\MediaAsset;

use Acme\Validation\Validators\MediaAsset as MediaAssetValidator;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;

// temporary aws+eloquent coupling
use \AWS;
use \DB;

use Acme\ClientException;
use Acme\ServerException;
use Acme\Model\Eloquent\MediaAsset as EloquentMediaAsset;
use Acme\Model\Eloquent\MediaAssetDerivative as EloquentMediaAssetDerivative;

class Attachment {
	/**
	 * @var MediaAssetValidator
	 */
	protected $mediaAssetValidator;

	/**
	 * @var S3
	 */
	protected $s3;

	/**
	 * @var Filesystem
	 */
	protected $filesystem;

	/**
	 * @var Config
	 */
	protected $config;

	public function __construct(
		MediaAssetValidator $mediaAssetValidator,
		Config $config,
		Filesystem $filesystem
	) {
		$this->mediaAssetValidator = $mediaAssetValidator;
		$this->config = $config;
		$this->filesystem = $filesystem;

		// TODO: remove internal dependencies
		$this->s3 = AWS::get('s3');
	}

	/**
	 * @param array $input MediaAsset record
	 * @param $file string local temp file path or URL (youtube, etc)
	 * @throws \Acme\Validation\Exception
	 */
	public function attach(array $input, $file)
	{
		$this->mediaAssetValidator->validate($input);

		try {
			$processor = ProcessorFactory::make($input, $this->config->get('acme.workspaceDir'));

			$asset = $this->persistAsset($input, $processor->process($file));

			$this->filesystem->delete($file);

			return $asset;
		} catch (ProcessorException $e) {
			// possible server error, log this
			throw new ServerException('Media Asset Processor failed: ' . $e->getMessage());
		} catch (FileException $e) {
			// client error, problem with file/resource
			throw new ServerException('Media Asset File exception: ' . $e->getMessage());
		} catch (InvalidProcessorException $e) {
			// red alert
			throw new ServerException('Invalid media asset processor: ' . $e->getMessage());
		}
	}

	/**
	 * TODO: break this out into testable db/cloud persistor classes
	 *
	 * @param array $meta input for attachment
	 * @param Asset $asset the processed asset
	 * @return Asset persisted asset
	 */
	public function persistAsset(array $meta, Asset $asset)
	{

		DB::beginTransaction();

		$eloquentMediaAsset = EloquentMediaAsset::create($meta);

		foreach($asset->getDerivatives() as $name => $derivative) {
			$derivative['media_asset_id'] = $eloquentMediaAsset->id;

			$sourceFile = $derivative['file'];
			unset($derivative['file']);

			$cloudPath = sprintf('MediaAsset/%s_%s.%s',
				$eloquentMediaAsset->id,
				$name,
				pathinfo($sourceFile, PATHINFO_EXTENSION));

			$derivative['uri'] = $this->config->get('acme.s3.url') . $cloudPath;

			$this->s3->putObject([
				'ACL'        => 'public-read',
				'Bucket'     => $this->config->get('acme.s3.bucket'),
				'Key'        => $cloudPath,
				'SourceFile' => $sourceFile
			]);

			$this->filesystem->delete($sourceFile);

			EloquentMediaAssetDerivative::create($derivative);
		}

		DB::commit();

		$result = EloquentMediaAsset::where('id', $eloquentMediaAsset->id)->with('derivatives')->first();

		return $result->toArray();
	}

	/**
	 * @param $s3 AWS::get('s3') instance
	 */
	public function setS3($s3) {
		$this->s3 = $s3;
	}
}
