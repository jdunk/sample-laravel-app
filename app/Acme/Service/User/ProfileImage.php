<?php namespace Acme\Service\User;


interface ProfileImage {
	public function setForUser($id, $file, $params = []);
	public function removeForUser($id);
	public function sideloadForUser($id, $url);
} 
