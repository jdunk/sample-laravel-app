<?php namespace Acme\Service;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;

use Acme\ServerException;

class Workspace
{
	/**
	 * @var string
	 */
	public $workspaceDir;

	public function __construct(
		Config $config,
		Filesystem $filesystem
	)
	{
		$this->config = $config;
		$this->filesystem = $filesystem;
	}

	public function init($baseDir, $namespace = null)
	{
		if (!$this->filesystem->isDirectory($baseDir))
			$this->filesystem->makeDirectory($baseDir);

		$workspaceDir = $this->uniqueDirectory($baseDir);

		if ($namespace)
			$workspaceDir = $namespace . '_' . $workspaceDir;

		$this->workspaceDir = rtrim($baseDir, '/') . '/' . $workspaceDir . '/';

		return $this->filesystem->makeDirectory($this->workspaceDir);
	}

	/**
	 * @param $file
	 * @throws \Exception
	 */
	public function validateFileExists($file)
	{
		if (!$this->filesystem->exists($file))
			throw new \Exception('Source file does not exist.');
	}

	/**
	 * Delete this workspace and its files
	 */
	public function cleanup()
	{
		$this->filesystem->deleteDirectory($this->workspaceDir);
	}

	/**
	 * Get a unique directory name inside of the workspace base dir
	 *
	 * @param $baseDir
	 * @return string unique, unused directory name
	 */
	public function uniqueDirectory($baseDir)
	{
		$randomString = str_random(16);
		$proposed = $baseDir . $randomString;

		if ($this->filesystem->isDirectory($proposed))
			return $this->uniqueDirectory($baseDir);

		return $randomString;
	}

	/**
	 * Get a unique, unused filepath and filename for a file with your desired extension.
	 *
	 * @param $ext string extension of unique filename
	 * @return string safe, unused filename with your extension
	 */
	public function uniqueWorkspaceFile($ext)
	{
		$randomString = str_random(16);
		$proposed = $this->workspaceDir . $randomString . '.' . $ext;

		if ($this->filesystem->exists($proposed))
			return $this->uniqueWorkspaceFilename($ext);

		return $proposed;
	}

	/**
	 * Get the full absolute path of a file in the workspace dir
	 *
	 * @param $name name of file in workspace dir
	 * @return string
	 */
	public function workspaceFile($name)
	{
		return $this->workspaceDir . $name;
	}

	/**
	 * Check if a filename exists in the workspace
	 *
	 * @param $filename
	 * @return bool true if the file exists
	 */
	public function isWorkspaceFile($filename)
	{
		return $this->filesystem->isFile($this->workspaceDir . $filename);
	}
}
