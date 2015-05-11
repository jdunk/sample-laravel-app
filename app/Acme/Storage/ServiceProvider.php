<?php namespace Acme\Storage;

use Illuminate\Support\ServiceProvider as CoreServiceProvider;

class ServiceProvider extends CoreServiceProvider {

	public function register() {
		
		$this->app->bind('Acme\Storage\Comment\CommentRepository', 
			'Acme\Storage\Comment\EloquentCommentRepository');

		$this->app->bind('Acme\Storage\Contest\ContestDesignerRepository',
			'Acme\Storage\Contest\EloquentContestDesignerRepository');

		$this->app->bind('Acme\Storage\Contest\ContestJudgeRepository',
			'Acme\Storage\Contest\EloquentContestJudgeRepository');

		$this->app->bind('Acme\Storage\Contest\ContestRegionRepository',
			'Acme\Storage\Contest\EloquentContestRegionRepository');

		$this->app->bind('Acme\Storage\Contest\ContestRepository',
			'Acme\Storage\Contest\EloquentContestRepository');

		$this->app->bind('Acme\Storage\Contest\DesignerVoteRepository',
			'Acme\Storage\Contest\EloquentDesignerVoteRepository');

		$this->app->bind('Acme\Storage\Contest\JudgeContestantRepository',
			'Acme\Storage\Contest\EloquentJudgeContestantRepository');

		$this->app->bind('Acme\Storage\Contest\JudgeRepository',
			'Acme\Storage\Contest\EloquentJudgeRepository');

		$this->app->bind('Acme\Storage\Designer\DesignerRepository',
			'Acme\Storage\Designer\EloquentDesignerRepository');

		$this->app->bind('Acme\Storage\EmailSubscriber\EmailSubscriberRepository',
			'Acme\Storage\EmailSubscriber\EloquentEmailSubscriberRepository');

		$this->app->bind('Acme\Storage\Flag\FlagRepository', 
			'Acme\Storage\Flag\EloquentFlagRepository');

		$this->app->bind('Acme\Storage\Flag\FlaggedItemRepository', 
			'Acme\Storage\Flag\EloquentFlaggedItemRepository');

		$this->app->bind('Acme\Storage\Follow\FollowRepository', 
			'Acme\Storage\Follow\EloquentFollowRepository');

		$this->app->bind('Acme\Storage\User\UserRepository', 
			'Acme\Storage\User\EloquentUserRepository');

		$this->app->bind('Acme\Storage\User\UserRoleRepository', 
			'Acme\Storage\User\EloquentUserRoleRepository');

	}

}
