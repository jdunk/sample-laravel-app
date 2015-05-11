<?php namespace Acme\Service\User;

use Acme\Validation\Validators\UserRegistration as UserRegistrationValidator;
use Acme\Validation\Validator;
use Acme\Storage\User\UserRepository;
use Acme\Service\OAuth\ProviderUser;
use Acme\Model\Eloquent\User;

class EloquentRegistration implements Registration
{

	protected $validator;
	protected $userRepository;
	protected $userModel;

	public function __construct(
		UserRegistrationValidator $validator,
		UserRepository $userRepository,
		User $userModel)
	{
		$this->validator = $validator;
		$this->userRepository = $userRepository;
		$this->userModel = $userModel;
	}

	public function process(array $attrs)
	{
		$this->validator->validate($attrs);

		$attrs['password'] = \Hash::make($attrs['password']);

		return $this->userRepository->create($attrs, ['username', 'password', 'name', 'email']);
	}

	public function usernameAvailable($username)
	{
		$qb = $this->userModel->newQuery();

		if ($qb->where('username', $username)->select('id')->first())
			return false;

		return true;
	}

	public function setRegistrationValidator(Validator $validator) {
		$this->validator = $validator;
	}

	public function suggestUsername($seed)
	{
		return $this->suggestUsernameFinder($seed, null);
	}

	protected function suggestUsernameFinder($seed, $i)
	{
		$proposedUsername = $seed . $i;
		if ($this->usernameAvailable($proposedUsername))
			return $proposedUsername;

		return $this->suggestUsernameFinder(substr($seed, 0, 15), mt_rand(100, 9999999));
	}
}
