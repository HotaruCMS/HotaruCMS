<?php

function hotaru_http_request($url)
{
	if(substr($url, 0, 4) != 'http')
	{
		return 'The URL provided is missing the "http", can not continue with request';
	}
	$req = $url;

	$pos = strpos($req, '://');
	$protocol = strtolower(substr($req, 0, $pos));

	$req = substr($req, $pos + 3);
	$pos = strpos($req, '/');
	if ($pos === false) {
		$pos = strlen($req);
	}
	$host = substr($req, 0, $pos);

	if (strpos($host, ':') !== false) {
		list($host, $port) = explode(':', $host);
	} else {
		$port = ($protocol == 'https') ? 443 : 80;
	}

	$uri = substr($req, $pos);
	if (empty($uri)) {
		$uri = '/';
	}

	$crlf = "\r\n";

	// generate request
	$req = 'GET '.$uri.' HTTP/1.0'.$crlf.'Host: '.$host.$crlf.$crlf;

	error_reporting(E_ERROR);

	// fetch
	$fp = fsockopen((($protocol == 'https') ? 'tls://' : '').$host, $port, $errno, $errstr, 20);
	if (!$fp) {
		return "BADURL";
	}

	fwrite($fp, $req);
	while (is_resource($fp) && $fp && !feof($fp)) {
		$response .= fread($fp, 1024);
	}
	fclose($fp);

	// split header and body
	$pos = strpos($response, $crlf.$crlf);
	if ($pos === false) {
		return($response);
	}
	$header = substr($response, 0, $pos);
	$body = substr($response, $pos + 2 * strlen($crlf));

	// parse headers
	$headers = array();
	$lines = explode($crlf, $header);
	foreach ($lines as $line) {
		if (($pos = strpos($line, ':')) !== false) {
			$headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos + 1));
		}
	}

	// redirection?
	if (isset($headers['location'])) {
		return hotaru_http_request($headers['location']);
	}

	return $body;
}

?>
