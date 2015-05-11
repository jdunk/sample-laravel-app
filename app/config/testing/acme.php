<?php

return array(
	'domain' => 'acme.dev',
	's3' => array(
		'region' => $_ENV['AWS_REGION'],
		'bucket' => $_ENV['S3_BUCKET'],
		// https://s3-us-west-1.amazonaws.com/acme-dev/media/backgrounds/bradsritter-bloom-lt.jpg
		'url'  => 'https://s3-us-west-1.amazonaws.com/'
	),
	'uploadTempChunk' => dirname(dirname(dirname(__FILE__))) . 'tests/tmp/chunks',
	'workspaceDir' => dirname(dirname(dirname(__FILE__))) . '/tests/tmp/workspace',
);
