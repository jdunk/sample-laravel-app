<?php namespace Acme\Utility;

use \AWS;

class SignedUrl {
	public static function getDownloadLink($bucketName, $fileName, $fileNameForDownload)  {
		$s3 = AWS::get('s3');
		$extra = urlencode("attachment; filename=\"$fileNameForDownload\"");
		$request = $s3->get("{$bucketName}/{$fileName}?response-content-disposition={$extra}");
		$url = $s3->createPresignedUrl($request, '+60 minutes');
		return $url;
	}
}
