<?php

/* **************************************************************************************************** 
 *  File: /admin/functions/funcs.arrays.php
 *  Purpose: A collection of functions for manipulating arrays.
 *  Notes: ---
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
 
/* ******************************************************************** 
 * Function: sksort
 * Parameters: associative array, key to sort from sub-array, "int" or "char", sort_order
 * Purpose: Sort an associative array by by the key of a sub-array.
 * Notes: http://us2.php.net/manual/en/function.ksort.php
 ********************************************************************** */
  
function sksort(&$array, $subkey="id", $type="int", $sort_ascending=false) {

    if (count($array))
        $temp_array[key($array)] = array_shift($array);

    foreach($array as $key => $val){
        $offset = 0;
        $found = false;
        foreach($temp_array as $tmp_key => $tmp_val)
        {
        	if($type == "int") {
        		if(!$found and ($val[$subkey]) > ($tmp_val[$subkey])) 
        		{
		                $temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
		                                            array($key => $val),
		                                            array_slice($temp_array,$offset)
		                                          );
		                $found = true;
			}
        	} else {
			if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey])) 
			{
				$temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
				                            array($key => $val),
				                            array_slice($temp_array,$offset)
				                          );
				$found = true;
			}        	
        	}

            $offset++;
        }
        if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
    }

    if ($sort_ascending) $array = array_reverse($temp_array);

    else $array = $temp_array;
    
    return $array;
}

?>