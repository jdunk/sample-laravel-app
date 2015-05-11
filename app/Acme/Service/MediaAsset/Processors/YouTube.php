<?php namespace Acme\Service\MediaAsset\Processors;

use \Acme\Service\MediaAsset\Asset;
use \Acme\Service\MediaAsset\Processor;

use \Image as Intervention;

use Illuminate\Filesystem\FileNotFoundException as LaravelFileNotFoundException;

/**
 * Class YouTube
 * @package Acme\Service\MediaAsset\Processors
 * @see highest res image http://img.youtube.com/vi/[video-id]/maxresdefault.jpg
 */
class YouTube extends Processor
{
	/**
	 * @var Asset
	 */
	protected $asset;

	/**
	 * @var string youtube video id
	 */
	protected $id;

	/**
	 * @var string url pattern for max res image
	 */
	protected $highResUrlPattern = 'http://img.youtube.com/vi/%s/maxresdefault.jpg';

	protected $normalizedUrlPattern = 'http://www.youtube.com/?v=%s';

	protected $derivativeSizes = [
		'large' => ['w' => 1500, 'h' => 1500, 'q' => 92],
		'medium' => ['w' => 750, 'h' => 750, 'q' => 85],
		'small' => ['w' => 375, 'h' => 375, 'q' => 78]
	];

	/**
	 * @param string $file youtube video url
	 * @return Asset
	 * @throws ProcessorException
	 */
	public function process($file)
	{
		$this->asset = new Asset();

		//$this->validateFileExists($file);
		$this->id = $this->youtubeIdFromUrl($file);

		$sourceFile = $this->workspaceFile('sourceFile.jpg');

		try {
			$this->Filesystem->put($sourceFile, $this->Filesystem->get(
				sprintf($this->highResUrlPattern, $this->id)));
		} catch (LaravelFileNotFoundException $e) {
			throw new ProcessorException('Could not retrieve YouTube thumbnail url for video at ' . $file);
		}

		/**
		 * This assumes that the highest res thumbnail image represents the highest res of
		 * of the video itself. Optionally
		 *
		 * @see http://stackoverflow.com/questions/9514635/get-youtube-video-dimensions-width-height
		 */
		$img = Intervention::make($sourceFile);

		$this->asset->addDerivative('video', [
			'file' => sprintf($this->normalizedUrlPattern, $this->id),
			'filesize' => null,
			'width' => $img->width(),
			'height' => $img->height(),
			'md5' => null
		]);

		$this->createDerivatives($sourceFile);

		return $this->asset;
	}

	public function getThumbnailUrlFromAPI($id)
	{
		$data = $this->Filesystem->get(sprintf('https://www.googleapis.com/youtube/v3/videos?key=%s&part=snippet&id=%s',
			$this->config->get('acme.google.publicApiKey'),
			$id));

		$json = json_decode($data);

		$bestImageUrl = sprintf("http://img.youtube.com/vi/%s/0.jpg", $id);

		// http://img.youtube.com/vi/e-ORhEE9VVg/maxresdefault.jpg

		$keys = ['maxres', 'high', 'standard'];

		foreach ($keys as $key) {
			if (empty($json->items[0]->snippet->thumbnails->$key->url)) {
				continue;
			}

			$bestImageUrl = $json->items[0]->snippet->thumbnails->$key->url;
			break;
		}

		return $bestImageUrl;
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
				function ($constraint) {
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

	/**
	 * @param $url
	 * @return mixed youtube video id or bool false if none found
	 */
	public function youtubeIdFromUrl($url)
	{
		$pattern =
			'%^# Match any youtube URL
		(?:https?://)?  # Optional scheme. Either http or https
		(?:www\.)?      # Optional www subdomain
		(?:             # Group host alternatives
		  youtu\.be/    # Either youtu.be,
		| youtube\.com  # or youtube.com
		  (?:           # Group path alternatives
			/embed/     # Either /embed/
		  | /v/         # or /v/
		  | /e/			# or /e/
		  | .*v=        # or /watch\?v=
		  )             # End path alternatives.
		)               # End host alternatives.
		([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
		($|&).*         # if additional parameters are also in query string after video id.
		$%x';

		$result = preg_match($pattern, trim($url), $matches);

		if (!empty($matches[1])) {
			return $matches[1];
		}

		return false;
	}
}
