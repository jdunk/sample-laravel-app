<?php namespace Acme\Service;

use Illuminate\Support\ServiceProvider as CoreServiceProvider;
use League\OAuth2\Client\Provider\Instagram;

use \Auth;
use \Config;
use \App;

use Acme\Service\OAuth\EloquentInstagramService;

class ServiceProvider extends CoreServiceProvider
{

	public function register()
	{

		/**
		 * Bind ARO authorization instance
		 */
		$this->app->bind('aro', function () {
			$roles = null;

			if (Auth::user()) {
				$userRoleRepository = App::build(
					'Acme\Storage\User\EloquentUserRoleRepository');

				$roles = $userRoleRepository->getRolesForUser(Auth::user()->id);
			}

			return new ACL\ARO($roles, Config::get('acl'));
		});

		$this->app->bind('Acme\Service\OAuth\InstagramService',
			'Acme\Service\OAuth\EloquentInstagramService');

		$this->app->bind('Acme\Service\User\Registration',
			'Acme\Service\User\EloquentRegistration');

		$this->app->bind('Acme\Service\User\ProfileImage',
			'Acme\Service\User\EloquentS3ProfileImage');

		$this->app->bind('League\OAuth2\Client\Provider\Instagram', function() {
			return new Instagram(array_replace_recursive(
				Config::get('acme.instagram'), [
				'scopes' => ['basic']
			]));
		});


		// bind other services to app...

		$this->app->bind('Acme\Service\Comment\CommentService', 'Acme\Service\Comment\EloquentCommentService');
		$this->app->bind('Acme\Service\Contest\ContestService', 'Acme\Service\Contest\EloquentContestService');
		$this->app->bind('Acme\Service\Flag\FlagService', 'Acme\Service\Flag\EloquentFlagService');
		$this->app->bind('Acme\Service\Follow\FollowService', 'Acme\Service\Follow\EloquentFollowService');
	}
}
