<?php namespace Acme\Model\Eloquent;

use \Illuminate\Auth\UserTrait;
use \Illuminate\Auth\UserInterface;

use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends \Eloquent implements UserInterface, RemindableInterface {

	use UserTrait;
	use RemindableTrait;

	protected $guarded = ['id'];
	protected $hidden = array('password', 'remember_token');
}
