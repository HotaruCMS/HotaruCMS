<?php
/**
 * A collection of functions for manipulating arrays
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
 
/**
 * Sort an associative array by by the key of a sub-array
 *
 * @param array $array associative array
 * @param string $subkey key to sort from sub-array
 * @param string $type "int" or "char"
 * @param bool $sort_ascending sort order
 * @return array
 *
 * Note: http://us2.php.net/manual/en/function.ksort.php
 */
  
function sksort(&$array, $subkey="id", $type="int", $sort_ascending=false)
{

    if (empty($array)) { return false; }
    
    if (count($array)) {
        $temp_array[key($array)] = array_shift($array);
    }

    foreach ($array as $key => $val)
    {
        $offset = 0;
        $found = false;
        foreach ($temp_array as $tmp_key => $tmp_val)
        {
            if ($type == "int") {
                if (!$found && ($val[$subkey]) > ($tmp_val[$subkey])) 
                {
                        $temp_array = array_merge(
                            (array)array_slice($temp_array,0,$offset),
                            array($key => $val),
                            array_slice($temp_array,$offset)
                        );
                        $found = true;
                }
            } else {
            
                if (!$found && strtolower($val[$subkey]) > strtolower($tmp_val[$subkey])) 
                {
                    $temp_array = array_merge(
                        (array)array_slice($temp_array,0,$offset),
                        array($key => $val),
                        array_slice($temp_array,$offset)
                    );
                    $found = true;
                }
            }

            $offset++;
        }
        if (!$found) {
            $temp_array = array_merge($temp_array, array($key => $val));
        }
    }

    if ($sort_ascending) { $array = array_reverse($temp_array); }

    else $array = $temp_array;
    
    return $array;
}

/**
 * Is in case insensitive array
 *
 * @link http://jp.php.net/array_unique 
 */
function in_iarray($str, $a)
{
    foreach($a as $v) {
        if (strcasecmp($str, $v) == 0) { return true;}
    }
    return false;
}


/**
 * Is unique in case insensitive array
 *
 * @link http://jp.php.net/array_unique 
 */
function array_iunique($a)
{
    $n = array();
    foreach ($a as $k=>$v) {
        if (!in_iarray($v, $n)) { $n[$k] = $v; }
    }
    return $n;
}


/**
 * Is serialized?
 *
 * @param mixed $data
 * @return bool 
 * @link http://www.weberdev.com/get_example-4099.html
 */
function is_serialized($data)
{
    if (!$data || !is_string($data)) {
        return false;
    }
    
    if (preg_match("/^(i|s|a|o|d)(.*);/si",$data)) {
        return true;
    }
    return false;
}


/**
 * Convert/Parse Object to Array
 *
 * @param $object
 * @return bool
 * @link http://forum.weblivehelp.net/web-development/php-convert-array-object-and-vice-versa-t2.html
 */
 function parse_object_to_array($object) {
   $array = array();
   if (is_object($object)) {
       foreach($object as $item)
         array_push($array, $item);
   }
   return $array;
}
?>