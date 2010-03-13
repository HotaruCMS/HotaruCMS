<?php
/**
 * Extends Inspekt with custom Hotaru methods
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
 
require_once(EXTENSIONS . 'Inspekt/Inspekt/AccessorAbstract.php');
 
class testAlnumLines extends AccessorAbstract {

   /**
    * a function to test for chars, digits, underscores and dashes.
    *
    * @return bool
    */
    protected function inspekt($val)
    {
        if (preg_match('/^([a-z0-9_-])+$/i', $val)) {
            return $val;
        } else {
            return false;
        }
   }
}


class testPage extends AccessorAbstract {

   /**
    * a function to test for a valid pagename
    *
    * @return bool
    */
    protected function inspekt($val)
    {
        if (preg_match('/^([a-z0-9\/_-])+$/i', $val)) {
            return $val;
        } else {
            return false;
        }
   }
}


class testUsername extends AccessorAbstract {

   /**
    * a function to test for a valid username
    *
    * @return bool
    */
    protected function inspekt($val)
    {
        if (preg_match('/^([a-z0-9_-]{4,32})+$/i', $val)) {
            return $val;
        } else {
            return false;
        }
   }
}


class testPassword extends AccessorAbstract {

   /**
    * a function to test for a valid password
    *
    * @return bool
    */
    protected function inspekt($val)
    {
        if (preg_match('/^([a-z0-9!@*#_-]{8,60})+$/i', $val)) {
            return $val;
        } else {
            return false;
        }
   }
}


class getFriendlyUrl extends AccessorAbstract {

   /**
    * a function to makea url friendly
    *
    * @return string
    */
    protected function inspekt($val)
    {
        return make_url_friendly($val);
   }
}


class sanitizeAll extends AccessorAbstract {

   /**
    * a function to sanitize a string with htmlentities and strip_tags
    *
    * @return string
    */
    protected function inspekt($val)
    {
        return sanitize($val, 'all');
   }
}


class sanitizeTags extends AccessorAbstract {

   /**
    * a function to sanitize a string with strip_tags
    *
    * @return string
    */
    protected function inspekt($val)
    {
        return sanitize($val, 'tags');
   }
}

class sanitizeEnts extends AccessorAbstract {

   /**
    * a function to sanitize with htmlentities
    *
    * @return string
    */
    protected function inspekt($val)
    {
        return sanitize($val, 'ents');
   }
}


class getHtmLawed extends AccessorAbstract {

   /**
    * a function to filter HTML
    *
    * @return string
    */
    protected function inspekt($text)
    {
        /*  make_tag_strict is OFF because we don't want to convert <u>, etc. to css 
            otherwise the strip_tags won't be able to allow them when requested in sanitize(). */
        $config = array('safe' => 1, 'make_tag_strict' => 0);
        
        // Allow plugins to alter the value of $config/
        // Plugins should return an array, e.g. array('safe' => 1); 
        require_once(BASE . 'Hotaru.php');
        $h = new Hotaru();
        $results = $h->pluginHook('hotaru_inspekt_htmlawed_config');
        if (is_array($results)) {
            foreach ($results as $res) {
                // THIS LOOKS WEIRD. IT NEEDS A RETHINK /Nick
                $config = $res; // $config takes on the value returned from the last plugin using this hook.
            }
        }
        
        require_once(EXTENSIONS . 'htmLawed/htmLawed.php');
        
        if (!get_magic_quotes_gpc()) {
            return htmLawed($text, $config);
        }
        else 
        {
            return htmLawed(stripslashes($text), $config);
        }
        return false;
   }
}

?>
