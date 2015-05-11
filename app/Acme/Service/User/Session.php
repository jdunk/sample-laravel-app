<?php namespace Acme\Service\User;

use Acme\Validation\Validators\Session as SessionValidator;
use Acme\Service\User\LoginException;

use \Auth;

class Session {

	protected $validator;

	public function __construct(SessionValidator $validator)
	{
		$this->validator = $validator;
	}

	public function create(array $attrs)
	{
		$this->validator->validate($attrs);

		$params = ['password' => $attrs['password']];

		if (strpos($attrs['login'], '@'))
			$params['email'] = $attrs['login'];
		else
			$params['username'] = $attrs['login'];

		if (! Auth::attempt($params, ! empty($attrs['remember_me']) ))
		{
			throw new LoginException('Invalid Login');
		}

		return Auth::user();
	}
}
