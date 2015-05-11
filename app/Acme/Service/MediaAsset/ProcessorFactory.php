<?php namespace Acme\Service\MediaAsset;

use Illuminate\Support\Facades\App as App;

class ProcessorFactory {

	/**
	 * @param array $input input from service
	 * @return mixed instance of processor for input's 'type'
	 * @throws InvalidProcessorException
	 */
	public static function make(array $input, $workspaceDir)
	{
		if (empty($input['type']))
			throw new InvalidProcessorException('No media processor type specified.');

		$processorFQCN = "Acme\\Service\\MediaAsset\\Processors\\" . $input['type'];

		if (!class_exists($processorFQCN, true))
			throw new InvalidProcessorException('No processor for '. $input['type']);

		$processor = App::build($processorFQCN);
		$processor->initializeWorkspace($workspaceDir);

		return $processor;
	}
}
