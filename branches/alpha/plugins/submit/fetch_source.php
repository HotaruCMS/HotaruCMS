<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/submit/fetch_source.php
 *  Purpose: For fetching content from the source url.
 *  Notes: This file is part of the Submit plugin. The main file is /plugins/submit/submit.php
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */
global $cage;

require_once('../../hotaru_header.php');
require_once(includes . 'SWCMS/class.httprequest.php');

$url = $cage->post->testURI('source_url');

if($url != 'http://' && $url != ''){
	$r = new HTTPRequest($url);
	$xxx = $r->DownloadToString();
	//echo "success"; exit;
} else {
	$xxx = '';
	//echo "failure"; exit;
}

if(preg_match("'<title>([^<]*?)</title>'", $xxx, $matches)) {
	$title = trim($matches[1]);
} else {
	$title = "No title found...";
}

if(preg_match("'<meta name=(\"|\')description(\"|\') content=(\"|\')(.*)(\"|\')'", $xxx, $matches)) {
	$content = trim($matches[4]);
} else {
	$content = "No description found...";
}

echo json_encode( array('title' => $title, 'content' => $content));

?>