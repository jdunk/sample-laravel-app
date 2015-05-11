<?php

use \Acme\Service\User\Registration as UserRegistration;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserSeeder');
	}

}

class UserSeeder extends Seeder {

	public function __construct(UserRegistration $userService)
	{
		$this->userService = $userService;
	}

	public function run()
	{
		DB::table('users')->truncate();
		DB::table('user_roles')->truncate();

		// NOTE: Use the service to ensure we are working with *valid* test data

		$user_array = $this->userService->process([
			'email' => 'foo@bar.com',
			'username' => 'tu_default',
			'name' => 'Default Test User',
			'password' => '1234567',
		]);

		$user_array = $this->userService->process([
			'email' => 'foo2@bar.com',
			'username' => 'tu_admin',
			'name' => 'Test Admin User',
			'password' => '1234567',
		]);

		// TODO: Replace below with service to ensure we are working with *valid* test data
		$urr = App::make('Acme\Storage\User\UserRoleRepository');

		// "tester" role should begin with 0 perms and have any perms dynamically added as desired by tests
		$urr->create([
			'user_id' => 1,
			'role' => 'tester' 
		]);

		$urr->create([
			'user_id' => 1,
			'role' => 'tester'
		]);

		$urr->create([
			'user_id' => 2,
			'role' => 'admin'
		]);
	}
}
