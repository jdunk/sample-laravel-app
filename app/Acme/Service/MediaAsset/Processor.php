<?php namespace Acme\Service\MediaAsset;

// @see http://taylorotwell.com/response-dont-use-facades/

use Illuminate\Support\Facades\File as File;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository as Config;

abstract class Processor
{
	/**
	 * @var string
	 */
	protected $workspaceDir;

	/**
	 * @var Filesystem
	 */
	protected $Filesystem;

	/**
	 * @var Config
	 */
	public $config;

	/**
	 * @param array $data
	 * @return Asset
	 */
	abstract public function process($file);

	public function __construct(Filesystem $filesystem, Config $config)
	{
		$this->Filesystem = $filesystem;
		$this->config = $config;
	}

	public function initializeWorkspace($baseDir)
	{
		if ($this->workspaceDir)
			$this->cleanup();

		if (!$this->Filesystem->isDirectory($baseDir))
			$this->Filesystem->makeDirectory($baseDir);

		$this->workspaceDir = rtrim($baseDir, '/') . '/' . $this->uniqueDirectory($baseDir) . '/';

		$this->Filesystem->makeDirectory($this->workspaceDir);
	}

	public function validateFileExists($file)
	{
		if (!file_exists($file))
			throw new FileException('Source file does not exist.');
	}

	public function cleanup()
	{
		$this->Filesystem->deleteDirectory($this->workspaceDir);
	}

	public function uniqueDirectory($baseDir)
	{
		$randomString = str_random(16);
		$proposed = $baseDir . $randomString;

		if ($this->Filesystem->isDirectory($proposed))
			return $this->uniqueDirectory($baseDir);

		return $randomString;
	}

	public function workspaceFile($name)
	{
		return $this->workspaceDir . $name;
	}

	public function isWorkspaceFile($filename)
	{
		return $this->Filesystem->isFile($this->workspaceDir . $filename);
	}
} 
