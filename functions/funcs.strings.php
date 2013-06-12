<?php
/**
 * A collection of functions for manipulating strings
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
 
// We need to set the default internal encoding for the functions to operate properly.
mb_internal_encoding("UTF-8");

/**
 * Truncate a string
 *
 * @param string $string
 * @param int truncate to X characters
 * @para, bool $dot adds ... if true
 * @return string
 */
function truncate($string, $chars=0, $dot=true)
{
	$length = mb_strlen($string);
	$truncated = mb_substr(strip_tags($string), 0, $chars); // strips tags to prevent broken tags

	if( $dot && ($length >= $chars) ) {
		$truncated .= '...';
	}

	return $truncated;
}


/**
 * Strip a string from the end of a string
 *
 * @param string $string
 * @param string $remove part of the string to strip
 * @return string
 */
function rstrtrim($str, $remove=null)
{
	$str = (string) $str;
	$remove = (string) $remove;
	
	if( empty($remove) ) {
		return rtrim($str);
	}
	
	$len = mb_strlen($remove);
	$offset = mb_strlen($str) - $len;
	
	while( $offset > 0 && $offset == mb_strpos($str, $remove, $offset) ) {
		$str = mb_substr($str, 0, $offset);
		$offset = mb_strlen($str) - $len;
	}
	
	return rtrim($str);
}

/**
 * Changes 'plugin_name' into 'Plugin Name'
 *
 * @param string $string e.g. a plugin folder name
 * @param string $delim - the character to replace underscores with
 * @return string
 */
function make_name($string, $delim = '_', $caps = true)
{
	$dep_array = array( );
	$dep_array = explode($delim, trim($string));
	if( $caps ) {
		$dep_array = array_map('ucfirst', $dep_array);
		$string = implode(' ', $dep_array);
	} else {
		$string = ucfirst(implode(' ', $dep_array));
	}
	
	return $string;
}


/**
 * Generates a random string
 *
 * @param int $length 
 * @return string
 * @link http://us2.php.net/manual/en/ref.strings.php (Moe 10-July-2007)
 */
function random_string($length = 8)
{
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxwz0123456789";
	$string = '';
	for( $i = 0; $i < $length; $i++ ) {
		$rand_key = mt_rand(0, strlen($chars));
		$string .= substr($chars, $rand_key, 1);
	}
	return str_shuffle($string);
}


/**
 * Sanitize input
 *
 * @param string $var the string to sanitize
 * @param string $santype type of sanitation: 'all', 'ents', 'tags'
 * @param string $allowable_tags
 * @return string|false
 *
 * Note: Borrowed from SWCMS
 */
function sanitize($var, $santype = 'all', $allowable_tags = '')
{
	// htmlentities & Strip tags
	if( $santype == 'all' ) {
		if( !get_magic_quotes_gpc() ) {
			return htmlentities(strip_tags($var, $allowable_tags), ENT_QUOTES, 'UTF-8');
		}
		return stripslashes(htmlentities(strip_tags($var, $allowable_tags), ENT_QUOTES, 'UTF-8'));
	}
	
	// Strip tags
	if( $santype == 'tags' ) {
		if( !get_magic_quotes_gpc() ) {
			return strip_tags($var, $allowable_tags);
		}
		return stripslashes(strip_tags($var, $allowable_tags));
	}
	
	// htmlentities
	if( $santype == 'ents' ) {
		if( !get_magic_quotes_gpc() ) {
			return htmlentities($var, ENT_QUOTES, 'UTF-8');
		}
		return stripslashes(htmlentities($var, ENT_QUOTES, 'UTF-8'));
	}

	return false;
}


/**
 * Make a url friendly - a dash-separated string
 *
 * @param string $input url to format
 * @return string|false
 *
 * Note: These functions seem to overlap each other a bit...
 */
function make_url_friendly($input)
{
	$output = replace_symbols($input);        
	$output = mb_substr($output, 0, 240);
	$output = mb_strtolower($output, 'UTF-8');
	$output = trim($output);    
	//From Wordpress and http://www.bernzilla.com/item.php?id=1007
	$output = sanitize_title_with_dashes($output);
	$output = urldecode($output); 
	
	if( $output ) {
		return $output;
	}

	return false;
}

/**
 * Replace symbols and ascii characters with simpler alternatives
 *
 * @param string $input
 * @return string
 *
 * Note: Adapted from SWCMS
 */
function replace_symbols($input)
{
	// FOR THIS TO WORK, THIS FUNCS.STRINGS.PHP FILE MUST BE SAVED 
	// IN UTF-8 CHARACTER ENCODING !!!
	// Replace spaces with hyphens
	$output = preg_replace('/\s+/', '-', $input);
	
	// Replace other characters
	$output = str_replace("--", "-", $output);      
	$output = str_replace("/", "", $output);
	$output = str_replace("\\", "", $output);
	$output = str_replace("'", "", $output);      
	$output = str_replace(",", "", $output);      
	$output = str_replace(";", "", $output);      
	$output = str_replace(":", "", $output);          
	$output = str_replace(".", "-", $output);      
	$output = str_replace("?", "", $output);      
	$output = str_replace("=", "-", $output);      
	$output = str_replace("+", "", $output); 
	$output = str_replace("$", "", $output);          
	$output = str_replace("&", "", $output);          
	$output = str_replace("!", "", $output);      
	$output = str_replace(">>", "-", $output);      
	$output = str_replace(">", "-", $output);      
	$output = str_replace("<<", "-", $output);      
	$output = str_replace("<", "-", $output);      
	$output = str_replace("*", "", $output);      
	$output = str_replace(")", "", $output);      
	$output = str_replace("(", "", $output);
	$output = str_replace("[", "", $output);
	$output = str_replace("]", "", $output);
	$output = str_replace("^", "", $output);    
	$output = str_replace("%", "", $output);
	$output = str_replace("#", "", $output);
	$output = str_replace("@", "", $output);
	$output = str_replace("`", "", $output);
	$output = str_replace("‘", "", $output);
	$output = str_replace("’", "", $output);
	$output = str_replace("“", "", $output);
	$output = str_replace("”", "", $output);
	$output = str_replace("~", "", $output);
	$output = str_replace("–", "-", $output);
	$output = str_replace("\"", "", $output);
	$output = str_replace("|", "", $output);
	$output = str_replace("«", "", $output);
	$output = str_replace("»", "", $output);
	$output = str_replace("‹", "", $output);
	$output = str_replace("›", "", $output);
	$output = str_replace("…", "", $output);
	$output = str_replace("--", "-", $output);
	$output = str_replace("---", "-", $output);
	$output = str_replace("—", "-", $output);
	
	return $output;
}


/**
 * Get rid of any dangerous or unwanted characters
 *
 * @param string $title
 *
 * Note: Borrowed from Wordpress
 */
function sanitize_title_with_dashes($title)
{
	$title = strip_tags($title);
	
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
	
	$title = remove_accents($title);
	
	$title = mb_strtolower($title, 'UTF-8');

	if( seems_utf8($title) ) {
		$title = utf8_uri_encode($title, 200);
	}
	
	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
	$title = preg_replace('/\s+/', '-', $title);
	$title = preg_replace('|-+|', '-', $title);
	$title = trim($title, '-');
	
	return $title;
}


/**
 * Remove accents from characters
 *
 * @param string $string
 * @return string
 *
 * Note: Borrowed from Wordpress
 */
function remove_accents($string) 
{
	if( !preg_match('/[\x80-\xff]/', $string) ) {
		return $string;
	}

	if( seems_utf8($string) ) {
		$chars = array(
		// Decompositions for Latin-1 Supplement
		chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
		chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
		chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
		chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
		chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
		chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
		chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
		chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
		chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
		chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
		chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
		chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
		chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
		chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
		chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
		chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
		chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
		chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
		chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
		chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
		chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
		chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
		chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
		chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
		chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
		chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
		chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
		chr(195).chr(191) => 'y',
		// Decompositions for Latin Extended-A
		chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
		chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
		chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
		chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
		chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
		chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
		chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
		chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
		chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
		chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
		chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
		chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
		chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
		chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
		chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
		chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
		chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
		chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
		chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
		chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
		chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
		chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
		chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
		chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
		chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
		chr(196).chr(178) => 'IJ', chr(196).chr(179) => 'ij',
		chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
		chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
		chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
		chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
		chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
		chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
		chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
		chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
		chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
		chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
		chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
		chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
		chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
		chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
		chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
		chr(197).chr(146) => 'OE', chr(197).chr(147) => 'oe',
		chr(197).chr(148) => 'R', chr(197).chr(149) => 'r',
		chr(197).chr(150) => 'R', chr(197).chr(151) => 'r',
		chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
		chr(197).chr(154) => 'S', chr(197).chr(155) => 's',
		chr(197).chr(156) => 'S', chr(197).chr(157) => 's',
		chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
		chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
		chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
		chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
		chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
		chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
		chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
		chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
		chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
		chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
		chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
		chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
		chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
		chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
		chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
		chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
		chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
		// Euro Sign
		chr(226).chr(130).chr(172) => 'E',
		// GBP (Pound) Sign
		chr(194).chr(163) => '' );
		
		$string = strtr($string, $chars);
	}
	else {
		// Assume ISO-8859-1 if not UTF-8
		$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
			.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
			.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
			.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
			.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
			.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
			.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
			.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
			.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
			.chr(252).chr(253).chr(255);

		$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
		
		$string = strtr($string, $chars['in'], $chars['out']);
		$double_chars['in'] = array( chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254) );
		$double_chars['out'] = array( 'OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th' );
		$string = str_replace($double_chars['in'], $double_chars['out'], $string);
    }

	return $string;
}


/**
 * Determine if the string is utf8
 *
 * @param string $str
 * @return bool
 *
 * Note: Borrowed from Wordpress (by bmorel at ssi dot fr )
 */
function seems_utf8($str)
{
	$length = strlen($str);
	for( $i = 0; $i < $length; $i++ ) {
		if( ord($str[$i]) < 0x80 ) {
			continue; // 0bbbbbbb 
		} elseif( (ord($str[$i]) & 0xE0) == 0xC0 ) {
			$n = 1; // 110bbbbb
		} elseif( (ord($str[$i]) & 0xF0) == 0xE0 ) {
			$n = 2; // 1110bbbb
		} elseif( (ord($str[$i]) & 0xF8) == 0xF0 ) {
			$n = 3; // 11110bbb
		} elseif( (ord($str[$i]) & 0xFC) == 0xF8 ) {
			$n = 4; // 111110bb
		} elseif( (ord($str[$i]) & 0xFE) == 0xFC ) {
			$n = 5; // 1111110b
		} else {
			return false; // Does not match any model
		}
		
		for( $j = 0; $j < $n; $j++ ) {
			// n bytes matching 10bbbbbb follow ?
			if( (++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80) ) {
				return false;
			}
		}
	}
	return true;
}


/**
 * Encodes a utf8 string
 *
 * @param string $utf8_string
 * @param int $length
 * @return string
 *
 * Note: Borrowed from Wordpress
 */
function utf8_uri_encode($utf8_string, $length = 0)
{
	$unicode = '';
	$values = array( );
	$num_octets = 1;
	$unicode_length = 0;

	$string_length = strlen($utf8_string);
	for( $i = 0; $i < $string_length; $i++ ) {
		$value = ord($utf8_string[$i]);
		
		if( $value < 128 ) {
			if( $length && ( $unicode_length >= $length ) ) {
				break;
			}
			$unicode .= chr($value);
			$unicode_length++;
		} else {
			if( count($values) == 0 ) {
				$num_octets = ( $value < 224 ) ? 2 : 3;
			}
			
			$values[] = $value;
			
			if( $length && ($unicode_length + ($num_octets * 3)) > $length ) {
				break;
			}
			
			if( count($values) == $num_octets ) {
				if( $num_octets == 3 ) {
					$unicode .= '%'.dechex($values[0]).'%'.dechex($values[1]).'%'.dechex($values[2]);
					$unicode_length += 9;
				} else {
					$unicode .= '%'.dechex($values[0]).'%'.dechex($values[1]);
					$unicode_length += 6;
				}
				
				$values = array( );
				$num_octets = 1;
			}
		}
	}

	return $unicode;
}


/**
 * Strip domain from url
 *
 * @param string $url
 * @return string|false $domain - including http://
 */
function get_domain($url = '')
{
	$parsed = parse_url($url);
	if( isset($parsed['scheme']) ) {
		$domain = $parsed['scheme']."://".$parsed['host'];
		return $domain;
	}
	
	return false;
}

if( !function_exists("iconv") ) {

	/**
	 * Convert string to requested character encoding if iconv library not installed
	 *
	 * @param string $from
	 * @param string $to
	 * @param string $string
	 * @return string
	 * @link http://www.jpfox.fr/?post/2007/07/25/165-alternative-a-la-fonction-php-iconv
	 */
	function iconv($from, $to, $string)
	{
		$converted = htmlentities($string, ENT_NOQUOTES, $from); 
		$converted = html_entity_decode($converted, ENT_NOQUOTES, $to);
		return $converted;
	}

}

/**
 * Count urls within a block of text
 *
 * @return int 
 * @link http://www.liamdelahunty.com/tips/php_url_count_check_for_comment_spam.php
 */
function countUrls($text = '')
{
	//$http = substr_count($text, "http");
	$href = substr_count($text, "href");
	$url = substr_count($text, "[url");
	
	return $href + $url;
}

/**
 * Convert textual urls into clickable HTML links, and add nofollow.
 *
 * @param string $string
 * @return string
 */
function make_urls_clickable($string) 
{
	$string = trim($string);
	// Super RegEx for converting urls into clickable links unless already in an anchor tag.	
	$string = preg_replace('|(?<!href=[\"\'])(https?://[A-Za-z0-9+\-=._/*(),@\'$:;&!?%]+)|i', '<a href="$1">$1</a>', $string);	
	// Add nofollow to links
	$string = preg_replace('/a[ ]+href[ ]*=[ ]*"http:\/\/(.*?)".*?/i', 'a href="http://$1" rel="nofollow"', $string);
	return $string;
}

/**
 * Strip foreign characters from latin1/utf8 database yuckiness
 *
 * @param string $str
 * @return string
 */
function strip_foreign_characters($str)
{
	$str = str_replace('Â', '', $str);
	$str = str_replace('â€™', '\'', $str);
	$str = str_replace('â€“', '-', $str);
	$str = str_replace('â€œ', '"', $str);
	$str = str_replace('â€', '"', $str);
	return $str;
}
?>