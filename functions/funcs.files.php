<?php

/*
function curPageURL() {
	$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
	$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
	$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
	$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
	return $url;
}

function curPageName() {
	if(isset($_SERVER["SCRIPT_NAME"])) {
		return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	} else {
		return "404.php";
	}
}
*/

function getFilenames($folder, $type='full') {	// Returns an array containing all the filenames in a folder
	$filenames = array();
	$handle = opendir($folder);
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != ".." && $file != ".svn") {
			if($type == 'full') {
	      			array_push($filenames, $folder . $file);	// 'full'
	      		} else {
	      			array_push($filenames, $file);		// 'short'
	      		}
	      	}
	}
	closedir($handle);
	return $filenames;
}

function stripAllFileExtensions($fileNames) {	// Takes an array of filenames, returns them without extensions
	$stripped = array();
	foreach($fileNames as $fileName) {
		array_push($stripped, stripFileExtension($fileName));
	}
	return $stripped;
}

function stripFileExtension($fileName) {	// Takes a single filename, returns it without an extension
	return strtok($fileName, ".");
}

?>