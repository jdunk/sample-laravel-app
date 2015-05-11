<?php
namespace Acme\Utility;

class CloudUri
{
	protected $uri;
	
	protected $provider;
	protected $bucket;
	protected $key;
	
	protected $providers = array(
		'amazon' => "@^https?:\/\/.*amazon[^\/]+\/([^\/]+)\/(.+)$@i",
		'dreamobjects' => "@^https?:\/\/([^\.]+).*dreamhost[^\/]+\/(.+)$@i"
	);
	
	public function __construct($uri = null) {
		if ($uri) $this->setUri($uri);
	}
	
	public static function uri($uri) {
		$i = new self($uri);
		if (!$i->initParts()) return false;
		
		return $i;
	}
	
	public function setUri($uri) {
		$this->uri = $uri;
	}
	
	public function initParts() {
		foreach ($this->providers as $provider => $pattern) {
			preg_match($pattern, $this->uri, $matches);
			if ($matches) {
				$this->provider = $provider;
				break;
			}
		}
		
		if (!$matches) return false;
		
		if (isset($matches[1]))
			$this->bucket = $matches[1];
		
		if (isset($matches[2]))
			$this->key = $matches[2];
		
		return $this;
	}
	
	public function getKey() {
		return $this->key;
	}
	
	public function getBucket() {
		return $this->bucket;
	}
}
