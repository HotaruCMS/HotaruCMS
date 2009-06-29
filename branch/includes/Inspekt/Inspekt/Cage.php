<?php
/**
 * Inspekt Cage - main source file
 *
 * @author Chris Shiflett <chris@shiflett.org>
 * @author Ed Finkler <coj@funkatron.com>
 *
 * @package Inspekt
 */

/**
 * require main Inspekt file
 */
require_once includes . 'Inspekt/Inspekt.php';


define ('ISPK_ARRAY_PATH_SEPARATOR', '/');

define ('ISPK_RECURSION_MAX', 10);

/**
 * @package Inspekt
 */
class Inspekt_Cage
{
	/**
	 * {@internal The raw source data.  Although tempting, NEVER EVER
	 * EVER access the data directly using this property!  Unfortunately,
	 * we can't deny access to this in PHP4}}
	 *
	 * Don't try to access this.  ever.
	 *
	 * @var array
	 */
	var $_source = NULL;


	var $_autofilter_conf = NULL;


	/**
     *
     * @return Inspekt_Cage
     */
	function Inspekt_Cage() {
		// placeholder -- we're using a factory here
	}



	/**
	 * Takes an array and wraps it inside an object.  If $strict is not set to
     * FALSE, the original array will be destroyed, and the data can only be
     * accessed via the object's accessor methods
	 *
     * @param array $source
     * @param string $conf_file
     * @param string $conf_section
     * @param boolean $strict
     * @return Inspekt_Cage
     *
     * @static
     */
	function Factory(&$source, $conf_file = NULL, $conf_section = NULL, $strict = TRUE) {

		if (!is_array($source)) {
			user_error('$source '.$source.' is not an array', E_USER_NOTICE);
		}

		$cage = new Inspekt_Cage();
		$cage->_setSource($source);
		$cage->_parseAndApplyAutoFilters($conf_file, $conf_section);

		if ($strict) {
			$source = NULL;
		}

		return $cage;
	}


	



	/**
	 * {@internal we use this to set the data array in Factory()}}
	 *
	 * @see Factory()
	 * @param array $newsource
	 */
	function _setSource(&$newsource) {

		if (!is_array($newsource)) {
			user_error('$source is not an array', E_USER_NOTICE);
		}

		$this->_source = $newsource;
	}


	function _parseAndApplyAutoFilters($conf_file, $conf_section)
	{
		if (isset($conf_file)) {
			$conf = parse_ini_file($conf_file, true);
			if ($conf_section) {
				if (isset($conf[$conf_section])) {
					$this->_autofilter_conf = $conf[$conf_section];
				}
			} else {
				$this->_autofilter_conf = $conf;
			}

			$this->_applyAutoFilters();
		}
	}
	

	function _applyAutoFilters() {

		if ( isset($this->_autofilter_conf) && is_array($this->_autofilter_conf)) {

			foreach($this->_autofilter_conf as $key=>$filters) {

				// get universal filter key
				if ($key == '*') {

					// get filters for this key
					$uni_filters = explode(',', $this->_autofilter_conf[$key]);
					array_walk($uni_filters, 'trim');

					// apply uni filters
					foreach($uni_filters as $this_filter) {
						foreach($this->_source as $key=>$val) {
							$this->_source[$key] = $this->$this_filter($key);
						}
					}
//					echo "<pre>UNI FILTERS"; echo var_dump($this->_source); echo "</pre>\n";

				} elseif($val = $this->keyExists($key)) {

					// get filters for this key
					$filters = explode(',', $this->_autofilter_conf[$key]);
					array_walk($filters, 'trim');

					// apply filters
					foreach($filters as $this_filter) {
						$this->_setValue($key, $this->$this_filter($key));
					}
//					echo "<pre> Filter $this_filter/$key: "; echo var_dump($this->_source); echo "</pre>\n";

				}
			}
		}
	}



	/**
     * Returns only the alphabetic characters in value.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag filter
     */
	function getAlpha($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		return Inspekt::getAlpha($this->_getValue($key));
	}

	/**
     * Returns only the alphabetic characters and digits in value.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag filter
     */
	function getAlnum($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		return Inspekt::getAlnum($this->_getValue($key));
	}

	/**
     * Returns only the digits in value. This differs from getInt().
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag filter
     */
	function getDigits($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		return Inspekt::getDigits($this->_getValue($key));
	}

	/**
     * Returns dirname(value).
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag filter
     */
	function getDir($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		return Inspekt::getDir($this->_getValue($key));
	}

	/**
     * Returns (int) value.
     *
     * @param mixed $key
     * @return int
     *
     * @tag filter
     */
	function getInt($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		return Inspekt::getInt($this->_getValue($key));
	}

	/**
     * Returns realpath(value).
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag filter
     */
	function getPath($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		return Inspekt::getPath($this->_getValue($key));
	}

	/**
     * Returns value.
     *
     * @param string $key
     * @return mixed
     *
     * @tag filter
     */
	function getRaw($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		return $this->_getValue($key);
	}

	/**
     * Returns value if every character is alphabetic or a digit,
     * FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testAlnum($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isAlnum($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if every character is alphabetic, FALSE
     * otherwise.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testAlpha($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isAlpha($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is greater than or equal to $min and less
     * than or equal to $max, FALSE otherwise. If $inc is set to
     * FALSE, then the value must be strictly greater than $min and
     * strictly less than $max.
     *
     * @param mixed $key
     * @param mixed $min
     * @param mixed $max
     * @param boolean $inc
     * @return mixed
     *
     * @tag validator
     */
	function testBetween($key, $min, $max, $inc = TRUE)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isBetween($this->_getValue($key), $min, $max, $inc)) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is a valid credit card number format. The
     * optional second argument allows developers to indicate the
     * type.
     *
     * @param mixed $key
     * @param mixed $type
     * @return mixed
     *
     * @tag validator
     */
	function testCcnum($key, $type = NULL)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isCcnum($this->_getValue($key), $type)) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns $value if it is a valid date, FALSE otherwise. The
     * date is required to be in ISO 8601 format.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testDate($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isDate($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if every character is a digit, FALSE otherwise.
     * This is just like isInt(), except there is no upper limit.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testDigits($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isDigits($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is a valid email format, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testEmail($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isEmail($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is a valid float value, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testFloat($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isFloat($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is greater than $min, FALSE otherwise.
     *
     * @param mixed $key
     * @param mixed $min
     * @return mixed
     *
     * @tag validator
     */
	function testGreaterThan($key, $min = NULL)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isGreaterThan($this->_getValue($key), $min)) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is a valid hexadecimal format, FALSE
     * otherwise.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testHex($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isHex($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is a valid hostname, FALSE otherwise.
     * Depending upon the value of $allow, Internet domain names, IP
     * addresses, and/or local network names are considered valid.
     * The default is HOST_ALLOW_ALL, which considers all of the
     * above to be valid.
     *
     * @param mixed $key
     * @param integer $allow bitfield for HOST_ALLOW_DNS, HOST_ALLOW_IP, HOST_ALLOW_LOCAL
     * @return mixed
     *
     * @tag validator
     */
	function testHostname($key, $allow = ISPK_HOST_ALLOW_ALL)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isHostname($this->_getValue($key), $allow)) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is a valid integer value, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testInt($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isInt($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is a valid IP format, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testIp($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isIp($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is less than $max, FALSE otherwise.
     *
     * @param mixed $key
     * @param mixed $max
     * @return mixed
     *
     * @tag validator
     */
	function testLessThan($key, $max = NULL)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isLessThan($this->_getValue($key), $max)) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is one of $allowed, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testOneOf($key, $allowed = NULL)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isOneOf($this->_getValue($key), $allowed)) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is a valid phone number format, FALSE
     * otherwise. The optional second argument indicates the country.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testPhone($key, $country = 'US')
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isPhone($this->_getValue($key), $country)) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it matches $pattern, FALSE otherwise. Uses
     * preg_match() for the matching.
     *
     * @param mixed $key
     * @param mixed $pattern
     * @return mixed
     *
     * @tag validator
     */
	function testRegex($key, $pattern = NULL)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isRegex($this->_getValue($key), $pattern)) {
			return $this->_getValue($key);
		}

		return FALSE;
	}


	/**
	 * Enter description here...
	 *
	 * @param unknown_type $key
	 * @return unknown
	 *
	 * @tag validator
	 */
	function testUri($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isUri($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value if it is a valid US ZIP, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag validator
     */
	function testZip($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		if (Inspekt::isZip($this->_getValue($key))) {
			return $this->_getValue($key);
		}

		return FALSE;
	}

	/**
     * Returns value with all tags removed.
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag filter
     */
	function noTags($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		return Inspekt::noTags($this->_getValue($key));
	}

	/**
     * Returns basename(value).
     *
     * @param mixed $key
     * @return mixed
     *
     * @tag filter
     */
	function noPath($key)
	{
		if (!$this->keyExists($key)) {
			return false;
		}
		return Inspekt::noPath($this->_getValue($key));
	}

	/**
     * Checks if a key exists
     *
     * @param mixed $key
     * @return bool
     *
     */
	function keyExists($key)
	{
		if (strpos($key, ISPK_ARRAY_PATH_SEPARATOR) !== FALSE) {
			$key = trim($key, ISPK_ARRAY_PATH_SEPARATOR);
			$keys = explode(ISPK_ARRAY_PATH_SEPARATOR, $key);
			return $this->_keyExistsRecursive($keys, $this->_source);
		} else {
			return array_key_exists($key, $this->_source);
		}
	}



	function _keyExistsRecursive($keys, $data_array) {
		$thiskey = current($keys);

		if (is_numeric($thiskey)) { // force numeric strings to be integers
			$thiskey = (int)$thiskey;
		}

		if ( array_key_exists($thiskey, $data_array) ) {
			if (sizeof($keys) == 1) {
				return true;
			} elseif ( is_array($data_array[$thiskey]) ) {
				unset($keys[key($keys)]);
				return $this->_keyExistsRecursive($keys, $data_array[$thiskey]);
			}
		} else { // if any key DNE, return false
			return false;
		}
	}


	/**
	 * Retrieves a value from the _source array
	 *
	 * @param string $key
	 * @return mixed
	 */
	function _getValue($key) {
		if (strpos($key, ISPK_ARRAY_PATH_SEPARATOR)!== FALSE) {
			$key = trim($key, ISPK_ARRAY_PATH_SEPARATOR);
			$keys = explode(ISPK_ARRAY_PATH_SEPARATOR, $key);
			return $this->_getValueRecursive($keys, $this->_source);
		} else {
			return $this->_source[$key];
		}
	}



	function _getValueRecursive($keys, $data_array, $level=0) {
		$thiskey = current($keys);

		if (is_numeric($thiskey)) { // force numeric strings to be integers
			$thiskey = (int)$thiskey;
		}

		if ( array_key_exists($thiskey, $data_array) ) {
			if (sizeof($keys) == 1) {
				return $data_array[$thiskey];
			} elseif ( is_array($data_array[$thiskey]) ) {
				if ($level < ISPK_RECURSION_MAX) {
					unset($keys[key($keys)]);
					return $this->_getValueRecursive($keys, $data_array[$thiskey], $level+1);
				} else {
					trigger_error('Recursion limit met', E_USER_WARNING);
					return false;
				}
			}
		} else { // if any key DNE, return false
			return false;
		}
	}


	/**
	 * Sets a value in the _source array
	 *
	 * @param mixed $key
	 * @param mixed $val
	 * @return mixed
	 */
	function _setValue($key, $val) {
		if (strpos($key, ISPK_ARRAY_PATH_SEPARATOR)!== FALSE) {
			$key = trim($key, ISPK_ARRAY_PATH_SEPARATOR);
			$keys = explode(ISPK_ARRAY_PATH_SEPARATOR, $key);
			return $this->_setValueRecursive($keys, $this->_source);
		} else {
			$this->_source[$key] = $val;
			return $this->_source[$key];
		}
	}


	function _setValueRecursive($keys, $val, $data_array, $level=0) {
		$thiskey = current($keys);

		if (is_numeric($thiskey)) { // force numeric strings to be integers
			$thiskey = (int)$thiskey;
		}

		if ( array_key_exists($thiskey, $data_array) ) {
			if (sizeof($keys) == 1) {
				$data_array[$thiskey] = $val;
				return $data_array[$thiskey];
			} elseif ( is_array($data_array[$thiskey]) ) {
				if ($level < ISPK_RECURSION_MAX) {
					unset($keys[key($keys)]);
					return $this->_setValueRecursive($keys, $val, $data_array[$thiskey], $level+1);
				} else {
					trigger_error('Recursion limit met', E_USER_WARNING);
					return false;
				}
			}
		} else { // if any key DNE, return false
			return false;
		}
	}


}
