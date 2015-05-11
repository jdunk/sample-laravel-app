<?php
class TestCase extends Illuminate\Foundation\Testing\TestCase
{
	
	/**
	 * Default preparation for each test
	 *
	 */
	public function setUp()
	{
		parent::setUp();
		
		$this->prepareForTests();
	}
	
	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;
		
		$testEnvironment = 'testing';
		
		return require __DIR__ . '/../../bootstrap/start.php';
	}
	
	/**
	 * Migrates the database and set the mailer to 'pretend'.
	 * This will cause the tests to run quickly.
	 *
	 */
	public function prepareForTests()
	{
		Artisan::call('migrate');
		Artisan::call('db:seed');
		Mail::pretend(true);
	}

	public function mockLogIn($user_id=1)
	{
		$user = Acme\Model\Eloquent\User::findOrFail($user_id);

		#Auth::shouldReceive('user')->andReturn($user);
		$this->be($user);
	}

	public function promoteAuthUserToAdmin()
	{
		$userRoleRepo = App::make('Acme\Storage\User\UserRoleRepository');
		$userRoleRepo->create(['user_id' => Auth::user()->id, 'role' => 'admin']);
	}
}
