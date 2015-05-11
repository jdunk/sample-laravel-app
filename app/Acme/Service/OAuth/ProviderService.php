<?php namespace Acme\Service\OAuth;

use Illuminate\Http\Request;

interface ProviderService {
	public function handleOauthReturn(Request $request, $aroUserId);
	public function getRedirectUrl();
}
