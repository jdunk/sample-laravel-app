<?php namespace Acme\Service\User;

use Acme\Service\OAuth\ProviderUser;
use Acme\Validation\Validator;
use Acme\Storage\User\UserRepository;

interface Registration {
	public function process(array $attrs);
	public function usernameAvailable($username);
	public function suggestUsername($seed);
	public function setRegistrationValidator(Validator $validator);
}
