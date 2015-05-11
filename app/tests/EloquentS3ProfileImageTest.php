<?php
use \Mockery as M;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
use Acme\Service\User\EloquentS3ProfileImage;


class EloquentS3ProfileImageTest extends TestCase
{
	public function testSetImageForUser()
	{


		$user = Acme\Model\Eloquent\User::create([
			'username' => 'sarah',
			'password' => '389d87hd3h',
			'email' => 'sarah@example.com',
			'name' => 'Sarah Fina'
		]);

		$Config = M::mock('Illuminate\Config\Repository');
		$Config
			->shouldReceive('get')
			->with('acme.s3.bucket')
			->andReturn('some-bucket');

		$Config
			->shouldReceive('get')
			->with('acme.s3.url')
			->andReturn('https://amazons3domain.com');

		$Config
			->shouldReceive('get')
			->with('acme.workspaceDir')
			->andReturn('/var/www/html/foo');

		$FileSystem = M::mock('Illuminate\Filesystem\Filesystem');
		$FileSystem
			->shouldReceive('exists')
			->andReturn(true);
		$FileSystem->shouldReceive('delete');

		$ImageManager = M::mock('Intervention\Image\ImageManager');
		$ImageManager->shouldReceive('make')->andReturnSelf();
		$ImageManager->shouldReceive('fit')->andReturnSelf();
		$ImageManager->shouldReceive('save');

		$Workspace = M::mock('Acme\Service\Workspace');
		$Workspace->shouldReceive('init')->withAnyArgs();
		$Workspace->shouldReceive('uniqueWorkspaceFile')->withAnyArgs();

		$S3 = $this->getMockBuilder('Aws\S3\S3Client')
			->disableOriginalConstructor()
			->setMethods(['putObject'])
			->getMock();

		$S3->expects($this->atLeastOnce())
			->method('putObject');

		$ProfileImage = new EloquentS3ProfileImage(
			$Config,
			$FileSystem,
			new Acme\Model\Eloquent\User(),
			$ImageManager,
			$Workspace
		);

		$ProfileImage->setS3($S3);

		$result = $ProfileImage->setForUser($user->id, '/some/file.jpg');

		$this->assertStringStartsWith('https://amazons3domain.com/some-bucket',
			$result['image_large']);

		$this->assertStringStartsWith('https://amazons3domain.com/some-bucket',
			$result['image_small']);
	}

	public function getInstance()
	{
		return App::build('Acme\Service\User\EloquentS3ProfileImage');
	}
}
