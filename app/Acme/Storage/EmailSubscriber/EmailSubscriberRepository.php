<?php namespace Acme\Storage\EmailSubscriber;

interface EmailSubscriberRepository {
	public function ensureCreated(array $input);
}
