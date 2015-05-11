<?php

return array(
	'domain' => 'acme.com',
	'title' => 'Acme',
	'systemFrom' => 'Acme',
	'systemEmail' => 'no-reply@acme.com',
	's3' => array(
		'region' => $_ENV['AWS_REGION'],
		'bucket' => $_ENV['S3_BUCKET'],
		// https://s3-us-west-1.amazonaws.com/acme-dev/media/backgrounds/bradsritter-bloom-lt.jpg
		'url'  => 'https://s3-us-west-1.amazonaws.com/'
	),
	'uploadTempChunk' => dirname(dirname(dirname(__FILE__))) . '/tmp/chunks',
	'workspaceDir' => dirname(dirname(dirname(__FILE__))) . '/tmp/workspace',
	'instagram' => array(
		'clientId' => $_ENV['INSTAGRAM_CLIENT_ID'],
		'clientSecret' => $_ENV['INSTAGRAM_CLIENT_SECRET'],
		'redirectUri' => $_ENV['INSTAGRAM_CLIENT_REDIRECT_URI']
	),
	'google' => array(
		'publicApiKey' => $_ENV['GOOGLE_PUBLIC_API_KEY']
	)
);
