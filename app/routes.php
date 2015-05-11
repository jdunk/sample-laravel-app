<?php

Route::pattern('id', '[0-9]+');

/**
 * Public (Production)
 */

Route::get('/', 'HomeController@showWelcome');
Route::post('/email_subscribers', 'EmailSubscriberController@store');

// END Public (Production)

/**
 * Authenticated (Production)
 */

Route::group(array(
	'before' => 'auth'
), function() {

});

// END Authenticated (Production)

/**
 * Public (Development Only)
 */

Route::group(array(
	'before' => 'dev'
), function() {

	Route::get('users/{user_id}/follows', 'FollowController@index');
	Route::get('users/{user_id}/followers', 'FollowController@followersIndex');
	Route::get('designers/{designer_id}/followers', 'FollowController@followersIndex');

	Route::resource('comments', 'CommentController', ['only' => ['index', 'show']]);
	Route::resource('contests', 'ContestController', ['only' => ['index', 'show']]);
	Route::resource('contest_regions', 'ContestRegionController', ['only' => ['index', 'show']]);

	// Instagram OAuth
	Route::get('/oauth/instagram/return', 'InstagramOauthController@oauthReturnHandler');
	Route::get('/oauth/instagram/redirect', 'InstagramOauthController@oauthRedirect');

	// User login/logout web controller routes
	Route::get('login', 'UserLoginController@show');
	Route::get('signup', 'UserRegistrationController@show');
	Route::get('logout', 'UserLoginController@logout');

	// Password Reminders
	Route::controller('password', 'RemindersController');

	// Local user signup
	Route::post('registrations', 'UserRegistrationController@store');
	Route::get('registrations/{username}', 'UserRegistrationController@checkUsernameAvailability');

	// Local user authentication
	Route::resource('sessions', 'SessionController', ['only' => ['store']]);

});

// END Public (Development Only)

/**
 * Authenticated (Development Only)
 */

Route::group(array(
	'before' => 'dev|auth'
), function() {

	Route::get('me', 'MeController@show');
	Route::post('/me/image', 'MeController@setImage');

	Route::post('flags', 'FlagController@store');
	Route::get('flagged_items', 'FlagController@index');
	Route::get('flagged_items/{id}', 'FlagController@show');
	Route::put('flagged_items/{id}', 'FlagController@update');

	Route::resource('me/follows', 'FollowController', ['only' => ['index', 'store', 'destroy', 'update']]);
	Route::resource('users/{user_id}/follows', 'FollowController', ['only' => ['store', 'destroy', 'update']]);
	Route::get('me/followers', 'FollowController@followersIndex');


	Route::resource('sessions', 'SessionController', ['only' => ['destroy']]);

	Route::resource('comments', 'CommentController', ['only' => ['store', 'destroy']]);
	Route::resource('contests', 'ContestController', ['only' => ['store', 'destroy', 'update']]);
	Route::resource('contest_regions', 'ContestRegionController', ['only' => ['store', 'destroy', 'update']]);
});

// END Authenticated (Development Only)


/**
 * @see http://laravel.com/docs/4.2/controllers#implicit-controllers
 * Route::controller('password', 'RemindersController');
 */
