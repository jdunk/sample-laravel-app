<?php namespace Acme\Service\OAuth;


class ProviderUser {
	protected $userData;

	protected $defaults = [
		'uid' => null,
		'nickname' => null,
		'name' => null,
		'firstName' => null,
		'lastName' => null,
		'email' => null,
		'location' => null,
		'description' => null,
		'imageUrl' => null,
		'urls' => null,
		'gender' => null,
		'locale' => null
	];

	public function __construct(array $userData) {
		$this->userData = array_replace_recursive($this->defaults, $userData);
	}

	public function get($key) {
		if (!array_key_exists($key, $this->userData))
			throw new \Exception('Invalid property name.');

		return $this->userData[$key];
	}

	public function getLocalUsernameSeed() {
		$seedKeys = ['nickname', 'name', 'firstName'];

		foreach($seedKeys as $seedKey) {
			$seed = $this->filterNonUsernameCharacters($this->get($seedKey));
			if (strlen($seed) > 1)
				return $seed;
		}

		// wow, you're super creative!
		return 'user';
	}

	public function filterNonUsernameCharacters($string) {
		return preg_replace("/[^a-zA-Z0-9]/", "", $string);
	}
} 
