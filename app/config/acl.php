<?php
/**
 * We are sacrificing granularity in place of security.  See 
 * [app]/Service/ACL/ARO
 */
return [
	'admin' => [
		'*.*'
	],
	'content_manager' => [
		'contests.*'
	],
	'ecom_admin' => [
		'merge:ecom_support'
	],
	'ecom_support' => [

	],
	'brand_admin' => [
		'merge:brand_support'
	],
	'brand_support' => [

	],
	'comm_admin' => [
		'merge:comm_moderator'
	],
	'comm_moderator' => [
		'user.suspend',
		'user.restore',
		'comment.delete',
		'post.delete',
		'review.delete'
	]
];
