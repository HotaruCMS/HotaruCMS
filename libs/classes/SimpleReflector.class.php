<?php

/**
 * An easy-to-use reflection class for exposing an object's identity so
 * problems can be debugged quickly.
 * 
 * It does correct null/true/false/empty string detection.  A string just gets
 * output directly, whereas objects will have their class interfaces exposed. 
 * Everything dumped to screen is correctly escaped for HTML output so the rest
 * of the page doesn't break.
 * 
 * The following information is displayed for objects:
 * 
 * - Class file/line location (especially useful when the same name class is defined multiple times in the filesystem)
 * - Methods in the class, including parameter names.
 * - Contents of the object (simple print_r that is HTML escaped)
 * 
 * Example Usage:
 * 
 * <code>
 * SimpleReflector::jam($object); // echo info to screen
 * $str = SimpleReflector::jam($object, true); // returns info as a string
 * SimpleReflector::jam($object, false, 'crazy object'); // echo info to screen with a custom title instead of "SimpleReflector"
 * </code>
 * 
 * For quicker use, consider adding a short function to your applications:
 * 
 * <code>
 * function jam() { $args = func_get_args(); call_user_func_array(array('SimpleReflector', 'jam'), $args); }
 * </code>
 * 
 * Then instead of typing `SimpleReflector::jam(...)` you can just type `jam(...)`.
 * 
 * @package default
 * @author Anthony Bush
 * @copyright Academic Superstore 2006-2008, FreeBSD (revised) licensed
 * @version 1.0 (2008-10-20) - First public release after 2 years of internal-only use.
 **/
class SimpleReflector
{
	/**
	 * Completely expose the contents of the given item in a way that makes it
	 * easy to find out more about that item.
	 * 
	 * It displays invisible characters (nulls, boolean, empty strings) and
	 * builds collapsable tables out of arrays and objects.
	 *
	 * @param mixed $var the object / variable to dump
	 * @param boolean $return set to true if you want it to return the output rather than echo it (just like print_r)
	 * @return mixed if $return is true, returns the output as string, otherwise it returns true.
	 * @author Anthony Bush
	 **/
	public static function jam($var, $return = false, $overrideTitle = '') {
		$html = '';
		if (is_array($var)) {
			$html .= self::jamObject($var, true, $overrideTitle);
		} else if (is_object($var)) {
			$html .= self::jamObject($var, true, $overrideTitle);
		} else {
			$html .= '<pre>';
			if (strlen($overrideTitle) > 0) {
				$html .= htmlentities($overrideTitle);
			} else {
				$html .= 'SimpleReflector';
			}
			$html .= ': ' . self::getVisual($var) . '</pre>';
		}
		
		if ($return) {
			return $html;
		} else {
			echo $html;
			return true;
		}
	}
	
	/**
	 * Shows where item is defined (if it's an object) dumps it's contents, and
	 * lists the public / private methods in a collapsable format.
	 * 
	 * @param mixed $var the object / variable to dump
	 * @param boolean $return set to true if you want it to return the output rather than echo it (just like print_r)
	 * @return mixed if $return is true, returns the output as string, otherwise it returns true.
	 * @author Anthony Bush
	 **/
	protected static function jamObject($var, $return = false, $overrideTitle = '') {
		$html  = '';
		static $num = 0;
		
		if (is_object($var))
		{
			
			$reflector = new ReflectionClass($var);
			
			$html .= self::getShowHideJavascript();
			
			$html .= '<div class="debug">';
			$html .= '<a class="title" href="javascript:void(showHide(\'superjam' . $num . '\'))">';
			if (strlen($overrideTitle) > 0) {
				$html .= htmlentities($overrideTitle);
			} else {
				$html .= 'SimpleReflector: ' . $reflector->getName();
			}
			$html .= '</a>';
			$html .= '<div id="superjam' . $num . '" class="superjam_results" style="display:none">';

			// Show where this class is defined:
			$html .= 'Definition: ' . $reflector->getFileName() . ':' . $reflector->getStartLine() . "<br />\n";
			
			// Get methods
			$methods = array(
				  'public' => array()
				, 'private' => array()
				, 'protected' => array()
			);
			foreach ($reflector->getMethods() as $method) {
				if ($method->isPrivate()) {
					$access = 'private';
				} elseif ($method->isProtected()) {
					$access = 'protected';
				} else {
					$access = 'public';
				}
				$methods[$access][$method->getName()] = $method;
			}
			foreach ($methods as $access => $accessMethods) {
				ksort($methods[$access]);
			}
			
			// Show methods
			ob_start();
			foreach ($methods as $access => $accessMethods) {
				if ( ! empty($accessMethods)) {
					echo '<a class="' . $access . '" href="javascript:void(showHide(\'superjam_' . $access . $num . '\'))">Show/Hide ' . ucwords($access) . ' Methods</a>' . "<br />\n";
					echo '<pre id="superjam_' . $access . $num . '" class="superjam_methods" style="display:none">';
					foreach ($accessMethods as $method) {
						$params = array();
						foreach ($method->getParameters() as $param) {
							$params[] = '<span class="methodParam">$' . $param->getName() . '</span>';
						}
						$paramNames = implode(', ', $params);
						printf(
							   '<div class="method ' . $access . '">'
							   . "%s%s%s "
							   . '<span class="methodName">'
							   . "%s</span>("
							   . $paramNames
							   . ');</div>'
							   , $method->isAbstract() ? ' abstract' : ''
							   , $method->isFinal() ? ' final' : ''
							   , $method->isStatic() ? ' static' : ''
							   , $method->getName()
							   );
					}
					echo '</pre>';
				}
			}
			$html .= ob_get_clean();
			
			
			// Show contents
			$html .= '<a class="superjam_contents" href="javascript:void(showHide(\'superjam_contents' . $num . '\'))">Show/Hide Contents</a>' . "<br />\n";
			$html .= '<pre id="superjam_contents' . $num . '" style="display:none">';
			$html .= htmlentities(print_r($var, true));
			$html .= '</pre>';
			
			$html .= '</div>'; // superjam . $num
			$html .= '</div>'; // debug
			
			$num++;
		}
		else if (is_array($var))
		{
			
			// Just show contents
			$html .= self::getShowHideJavascript();
			$html .= '<div class="debug" style="border: 1px solid #000; background: #fff">';
			$html .= '<a class="title" style="display: block; background: #ddd; padding: 5px; font-weight: bold;" href="javascript:void(showHide(\'superjam' . $num . '\'))">';
			if (strlen($overrideTitle) > 0) {
				$html .= htmlentities($overrideTitle);
			} else {
				$html .= 'SimpleReflector: PHP Array';
			}
			$html .= '</a>';
			$html .= '<pre id="superjam' . $num . '" style="display:none; padding: 5px">';
			$html .= htmlentities(print_r($var, true));
			$html .= '</pre>';
			$html .= '</div>'; // debug
			
			$num++;
		}
		
		if ($return) {
			return $html;
		} else {
			echo $html;
			return true;
		}
		
	}
	
	protected static function getAncestors($class) {
		$classes = array($class);
		while ($class = get_parent_class($class)) {
			$classes[] = $class;
		}
		return $classes;
	}
	
	protected static function getVisual($var) {
		if (is_null($var)) {
			return '[null]';
		} else if ($var === true) {
			return '[true]';
		} else if ($var === false) {
			return '[false]';
		} else if ($var === '') {
			return '[empty string]';
		} else if (is_array($var)) {
			return self::jamObject($var, true);
		} else if (is_object($var)) {
			return self::jamObject($var, true);
		} else {
			return htmlentities($var);
		}
	}
	
	/**
	 * Internal function for printing out style / javascript that makes the
	 * collapsing features work.
	 * 
	 * @return string
	 * @author Anthony Bush
	 **/
	protected static function getShowHideJavascript() {
		static $called = false;
		if ($called) {
			return;
		}
		$called = true;
		ob_start();
		?>
		<style type="text/css" media="screen">
		/* <![CDATA[ */
			.debug {
				border: 1px solid #000;
				background: #fff;
				color: #000;
			}
			.debug,
			.debug table td {
				text-align: left;
			}
			.debug table {
				margin: .5em 0;
			}
			.debug .title {
				display: block;
				background: #ddd;
				padding: 5px;
				font-weight: bold;
			}
			.debug a:link,
			.debug a:visited,
			.debug a:hover,
			.debug a:active {
				color: #0000A2;
				font-weight: bold;
			}
			.debug .superjam_methods {
				display: none;
			}
			.debug .superjam_results {
				display: none;
				padding: 5px;
			}
			.debug .methodName {
				color: #9D6F38;
			}
		/* ]]> */
		</style>
		<script type="text/javascript" language="javascript" charset="utf-8">
		// <![CDATA[
			function showHide(elementId) {
				e = document.getElementById(elementId);
				if (e.style.display == 'none') {
					e.style.display = 'block';
				} else {
					e.style.display = 'none';
				}
			}
		// ]]>
		</script>
		<?php
		return ob_get_clean();
	}	
}

?>