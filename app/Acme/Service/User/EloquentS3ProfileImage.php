<?php namespace Acme\Service\User;

use Acme\Utility\CloudUri;
use Acme\Service\Base;
use Acme\Service\Workspace;
use Acme\ClientException;
use Acme\ServerException;
use Acme\Model\Eloquent\User as EloquentUser;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
use Illuminate\Filesystem\FileNotFoundException;

use \Hash;
use \AWS;
use \Image;

class EloquentS3ProfileImage extends Base implements ProfileImage
{
	/**
	 * @var Config
	 */
	protected $config;

	/**
	 * @var EloquentUser
	 */
	protected $userModel;

	/**
	 * @var Filesystem
	 */
	protected $filesystem;

	/**
	 * @var Workspace
	 */
	protected $workspace;

	/**
	 * @var ImageManager
	 */
	protected $intervention;

	protected $dimensions = [
		'image_large' => [
			'w' => 640,
			'h' => 640,
			'q' => 80
		],
		'image_small' => [
			'w' => 128,
			'h' => 128,
			'q' => 75
		]
	];

	/**
	 * @var S3
	 */
	protected $s3;

	public function __construct(
		Config $config,
		Filesystem $filesystem,
		EloquentUser $userModel,
		ImageManager $intervention,
		Workspace $workspace
	)
	{
		$this->config = $config;
		$this->filesystem = $filesystem;
		$this->userModel = $userModel;
		$this->intervention = $intervention;
		$this->workspace = $workspace;

		$this->s3 = AWS::get('s3');
	}

	public function setForUser($id, $file, $params = [])
	{
		$qb = $this->userModel->newQuery();
		$u = $qb->where('id', $id)->select([
			'id',
			'image_large',
			'image_small'
		])->first();

		if (!$u)
			throw new ClientException('Invalid user id');

		if (is_a($file, 'Intervention\Image\Image')) {
			$sourceImage = $file;
		} elseif (is_string($file) && $this->filesystem->exists($file)) {
			$sourceImage = $this->intervention->make($file);
			$this->filesystem->delete($file);
		} else {
			throw new ServerException('Invalid file provided to setForUser');
		}

		$this->workspace->init($this->config->get('acme.workspaceDir'));
		$tempImageFile = $this->workspace->uniqueWorkspaceFile('jpg');

		$this->removeForUser($id);

		$rand = substr(md5(mt_rand(0, 1000000) . microtime()), 0, 20);

		foreach ($this->dimensions as $name => $dimensions) {
			$s3Filename = '/profile/' . $id . '-' . $name . '-' . $rand . '.jpg';

			$img = $this->intervention->make($sourceImage)
				->fit($dimensions['w'], $dimensions['h'])
				->save($tempImageFile, $dimensions['q']);

			$this->s3->putObject(array(
				'ACL' => 'public-read',
				'Bucket' => $this->config->get('acme.s3.bucket'),
				'Key' => $s3Filename,
				'SourceFile' => $tempImageFile
			));

			$url = rtrim($this->config->get('acme.s3.url'), '/')
				. '/'
				. $this->config->get('acme.s3.bucket')
				. $s3Filename;

			$u->$name = $url;

			$this->filesystem->delete($tempImageFile);
		}

		$this->filesystem->delete($file);

		$u->save();
		return $u->toArray();
	}

	public function sideloadForUser($id, $url)
	{
		$this->workspace->init($this->config->get('acme.workspaceDir'));
		$sourceFile = $this->workspace->uniqueWorkspaceFile('jpg');

		try {
			$this->filesystem->put($sourceFile, $this->filesystem->get($url));
			return $this->setForUser($id, $sourceFile);
		} catch (FileNotFoundException $e) {
			// it happens...
			return false;
		}

	}

	public function removeForUser($id)
	{
		$qb = $this->userModel->newQuery();
		$user = $qb->where('id', $id)->select([
			'id',
			'image_large',
			'image_small'
		])->first();

		$imageUrlColumns = ['image_large', 'image_small'];

		foreach ($imageUrlColumns as $col) {
			if (!$user->$col)
				continue;

			$cloudItem = CloudUri::uri($user->$col);
			if (!$cloudItem)
				throw new ServerException('There was a problem parsing the cloud URI: ' . $user->$col);

			$s3 = AWS::get('s3');
			$result = $s3->deleteObject(array(
				'Bucket' => $cloudItem->getBucket(),
				'Key' => $cloudItem->getKey()
			));
		}

		return true;
	}

	public function setS3($s3) {
		$this->s3 = $s3;
	}

}
