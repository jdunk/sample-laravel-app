<?php

/**
 * Validation Exception
 */
use Acme\Validation\Exception as ValidationException;

App::error(function (ValidationException $exception) {
	if (Request::ajax()) {
		return Response::json(array(
			'message' => 'Please correct the fields and try again.',
			'errors' => $exception->getErrors()->toArray()
		), 400);
	}

	return Response::make('Please correct the fields and try again.', 400);
});

/**
 * Generic Client Exception
 */
use Acme\ClientException as ClientException;

App::error(function (ClientException $exception) {
	if (Request::ajax()) {
		return Response::json(array(
			'message' => $exception->getMessage()
		), 400);
	}

	return Response::make('exceptions.client', ['message' => $exception->getMessage()]);
});

/**
 * Model 404 from Eloquent findOrFail
 */
use Illuminate\Database\Eloquent\ModelNotFoundException;

App::error(function (ModelNotFoundException $e) {
	if (Request::ajax())
		return Response::json(array('message' => 'Item not found.'), 404);

	return Response::make('Item not Found', 404);
});

/**
 * Not sure exactly what this does.  Just a custom 404 handler?
 */
App::missing(function ($exception) {
	if (Request::ajax())
		return Response::json(array('message' => 'Item not found.'), 404);

	return Response::make('Invalid URL', 404);
});

/**
 * Log fatal PHP errors
 */
App::fatal(function ($exception) {
	// PHP errors
	Log::error($exception);
});

/**
 * ACL Authorization Exception
 */
use Acme\Service\ACL\AuthorizationException;

App::error(function (AuthorizationException $exception) {
	if (Request::ajax()) {
		return Response::json(array(
			'message' => 'Access to this resource is restricted.'), 403);
	}

	return Response::make('Access to this resource is restricted.', 403);
});

/**
 * Log any uncaught Exception
 */
App::error(function (Exception $exception, $code) {
	Log::error($exception);
});
