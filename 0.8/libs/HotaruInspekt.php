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
        if (preg_match('/^([a-z0-9@*#_-]{8,60})+$/i', $val)) {
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


class getMixedString1 extends AccessorAbstract {

   /**
    * a function to sanitize a string with htmlentities
    *
    * @return string
    */
    protected function inspekt($val)
    {
        return sanitize($val, 1);
   }
}


class getMixedString2 extends AccessorAbstract {

   /**
    * a function to sanitize a string without htmlentities
    *
    * @return string
    */
    protected function inspekt($val)
    {
        return sanitize($val, 2);
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
        $config = array('safe' => 1);
        
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