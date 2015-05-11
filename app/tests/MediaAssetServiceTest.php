<?php
use \Mockery as M;
use \Image;
use \AWS;

use Illuminate\Filesystem\Filesystem;

use Acme\Service\MediaAsset\Attachment;
use Acme\Service\MediaAsset\ProcessorFactory;
use Acme\Model\Eloquent\User;

class MediaAssetServiceTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();

		Schema::drop('users');

		Schema::create('users', function ($table) {
			$table->increments('id');
			$table->string('username', 100);
			$table->timestamps();
		});
	}

	public function tearDown()
	{
		parent::tearDown();

		Schema::drop('users');
	}

	/**
	 * @covers \Acme\Service\MediaAsset\ProcessorFactory::make
	 * @throws \Acme\Service\MediaAsset\InvalidProcessorException
	 */
	public function testProcessorFactory()
	{
		$workingDir = dirname(__FILE__) . '/tmp';
		$factory = ProcessorFactory::make(['type' => 'Image'], $workingDir);

		$this->assertEquals(get_class($factory), 'Acme\Service\MediaAsset\Processors\Image');

		$fs = new Filesystem();
		$fs->cleanDirectory($workingDir);

		// bad processor
		$exceptionThrown = false;

		try {
			$factory = ProcessorFactory::make(['type' => 'ThisWillNeverHappen'], $workingDir);
		} catch (Acme\Service\MediaAsset\InvalidProcessorException $e) {
			$exceptionThrown = true;
		}

		$this->assertTrue($exceptionThrown);
	}

	/**
	 * @covers Acme\Service\MediaAsset\Attachment::attach
	 */
	public function testAttachImageFilesystemIntegration()
	{
		$tmpDir = dirname(__FILE__) . '/tmp';

		$fs = new Filesystem();
		$fs->cleanDirectory($tmpDir);

		$s3 = Mockery::mock('service');
		$s3->shouldReceive('putObject')->once()->withAnyArgs();

		$attachmentSvc = App::build('Acme\Service\MediaAsset\Attachment');
		$attachmentSvc->setS3($s3);

		User::create([
			'username' => 'iuploadstuff'
		]);

		$file = $tmpDir . '/testImage.jpg';

		$img = Image::canvas(800, 600, '#ccc');
		$img->save($file, 40);

		$asset = $attachmentSvc->attach([
			'media_assetable_type' => 'Post',
			'media_assetable_id' => 1,
			'user_id' => 1,
			'type' => 'Image',
			'title' => 'image000000001.jpg',
			'caption' => 'Trees'
		], $file);

		// not being too restrictive here
		//var_dump('asset:', $asset);

		$this->assertTrue(count($asset['derivatives']) > 0);
		$this->assertEquals(strlen($asset['derivatives'][0]['md5']), 32);

		$fs->cleanDirectory($tmpDir);

		// haha
		$fs->put($tmpDir . '/.gitignore', '*\n!.gitignore');
	}

	/**
	 * @covers Acme\Service\MediaAsset\Processors\YouTube::youtubeIdFromUrl
	 */
	public function testYouTubeURLParser()
	{
		$ytp = App::build('Acme\Service\MediaAsset\Processors\YouTube');

		$id = 'dQw4w9WgXcQ';

		$this->assertEquals($ytp->youtubeIdFromUrl('http://youtu.be/dQw4w9WgXcQ'), $id);
		$this->assertEquals($ytp->youtubeIdFromUrl('http://www.youtube.com/?v=dQw4w9WgXcQ'), $id);
		$this->assertEquals($ytp->youtubeIdFromUrl('http://www.youtube.com/?v=dQw4w9WgXcQ&feature=player_embedded'), $id);
		$this->assertEquals($ytp->youtubeIdFromUrl('http://www.youtube.com/watch?v=dQw4w9WgXcQ'), $id);
		$this->assertEquals($ytp->youtubeIdFromUrl('http://www.youtube.com/watch?v=dQw4w9WgXcQ&feature=player_embedded'), $id);
		$this->assertEquals($ytp->youtubeIdFromUrl('http://www.youtube.com/v/dQw4w9WgXcQ'), $id);
		$this->assertEquals($ytp->youtubeIdFromUrl('http://www.youtube.com/e/dQw4w9WgXcQ'), $id);
		$this->assertEquals($ytp->youtubeIdFromUrl('http://www.youtube.com/embed/dQw4w9WgXcQ'), $id);
		$this->assertEquals($ytp->youtubeIdFromUrl('http://www.youtube.com/embed/dQw4w9WgXcQ '), $id);
		$this->assertEquals($ytp->youtubeIdFromUrl(' http://www.youtube.com/embed/dQw4w9WgXcQ'), $id);

	}

	/**
	 * @covers Acme\Service\MediaAsset\Processors\YouTube::getThumbnailUrlFromAPI
	 */
	public function testYouTubeThumbnailSideload() {
		$id = 'e-ORhEE9VVg';

		$Filesystem = M::mock('Illuminate\Filesystem\Filesystem');
		$Filesystem
			->shouldReceive('get')
			->withAnyArgs()
			->andReturn(json_encode($this->exampleYoutubeResponseJson()));

		$Config = M::mock('Illuminate\Config\Repository');
		$Config
			->shouldReceive('get')
			->with('acme.google.publicApiKey');

		$ytp = new Acme\Service\MediaAsset\Processors\YouTube(
			$Filesystem,
			$Config
		);

		$result = $ytp->getThumbnailUrlFromAPI($id);

		$this->assertEquals("https://i.ytimg.com/vi/$id/maxresdefault.jpg", $result);
	}

	public function exampleYoutubeResponseJson() {
		$response = new \stdClass();
		$response->items[0] = new \stdClass();
		$response->items[0]->snippet = new stdClass();
		$response->items[0]->snippet->thumbnails = new stdClass();

		$thumbOne = new \stdClass();
		$thumbOne->url = 'https://i.ytimg.com/vi/e-ORhEE9VVg/default.jpg';
		$thumbOne->width = 120;
		$thumbOne->height = 90;
		$response->items[0]->snippet->thumbnails->default = $thumbOne;

		$thumbTwo = new \stdClass();
		$thumbTwo->url = 'https://i.ytimg.com/vi/e-ORhEE9VVg/hqdefault.jpg';
		$thumbTwo->width = 480;
		$thumbTwo->height = 360;
		$response->items[0]->snippet->thumbnails->high = $thumbTwo;

		$thumbThree = new \stdClass();
		$thumbThree->url = 'https://i.ytimg.com/vi/e-ORhEE9VVg/maxresdefault.jpg';
		$thumbThree->width = 1280;
		$thumbThree->height = 720;
		$response->items[0]->snippet->thumbnails->maxres = $thumbThree;

		return $response;
	}
}
