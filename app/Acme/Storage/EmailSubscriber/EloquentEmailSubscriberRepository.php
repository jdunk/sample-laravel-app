<?php namespace Acme\Storage\EmailSubscriber;

use Acme\Model\Eloquent\EmailSubscriber;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;
use Acme\Validation\Validators\EmailSubscriber as EmailSubscriberValidator;

class EloquentEmailSubscriberRepository
	extends EloquentBaseRepository
	implements EmailSubscriberRepository {

	/**
	 * @var EmailSubscriberValidator
	 */
	protected $emailSubscriberValidator;

	public function __construct(EmailSubscriber $model, EmailSubscriberValidator $emailSubscriberValidator)
	{
		$this->model = $model;
		$this->emailSubscriberValidator = $emailSubscriberValidator;
	}

	public function ensureCreated(array $input)
	{
		$this->emailSubscriberValidator->validate($input);

		$existing = $this->model->where('email', $input['email'])->first();

		if ($existing)
			return $existing;

		return $this->create($input, ['email', 'ip']);
	}
} 
