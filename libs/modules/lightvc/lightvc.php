<?php
/**
 * LightVC - A lightweight view-controller framework.
 * http://lightvc.org/
 * 
 * You provide your own model/ORM. We recommend Cough <http://coughphp.com>.
 * 
 * The purpose of this framework is to provide just a "view-controller"
 * setup without all the other junk. Ideally, the classes from other frameworks
 * should be reusable but instead they are mostly coupled with their frameworks.
 * It's up to you to go get those classes if you need them, or provide your own.
 * 
 * Additionally, we've decoupled it from any sort of Model so that you can use
 * the one you already know and love. And if you don't know one, now is a great
 * time to check out CoughPHP. Other ORMs can be found at:
 * 
 * http://en.wikipedia.org/wiki/List_of_object-relational_mapping_software#PHP
 * 
 * By providing just the VC, we increase the reusability of not only the
 * framework itself, but non-framework components as well.
 * 
 * The framework is fast. Currently the speed of this framework is unmatched by
 * any other PHP framework available today.
 * 
 * You get to use the classes you've already been using without worrying about
 * naming conflicts or inefficiencies from loading both your classes and the
 * classes from some other framework.
 * 
 * LightVC aims to be easier to use, more configurable, and light in footprint.
 * 
 * @author Anthony Bush
 * @package lightvc
 * @see http://lightvc.org/
 **/

/**
 * Configuration class for the LVC suite of classes.
 *
 * @package lightvc
 * @author Anthony Bush
 * @since 2007-04-20
 **/
class Lvc_Config {
	protected static $controllerPaths = array();
	protected static $controllerSuffix = '.php'; // e.g. _controller.php
	protected static $controllerViewPaths = array();
	protected static $controllerViewSuffix = '.php'; // e.g. .tpl.php
	protected static $layoutViewPaths = array();
	protected static $layoutViewSuffix = '.php'; // e.g. .tpl.php
	protected static $elementViewPaths = array();
	protected static $elementViewSuffix = '.php'; // e.g. .tpl.php
	protected static $viewClassName = 'Lvc_View'; // e.g. AppView
	protected static $layoutContentVarName = 'layoutContent'; // e.g. content_for_layout
	
	/**
	 * Sets whether or not to send action params as an array or as arguments
	 * to the function.
	 * 
	 * true => action($params)
	 * false => action($param1, $param2, $param3, ...)
	 *
	 * @var boolean
	 **/
	protected static $sendActionParamsAsArray = false;
	
	// These may be moved into some sort of routing thing later. For now:
	
	/**
	 * The controller name to use if no controller name can be gathered from the request.
	 *
	 * @var string
	 **/
	protected static $defaultControllerName = 'page';
	/**
	 * The action name to call on the defaultControllerName if no controller name can be gathered from the request.
	 *
	 * @var string
	 **/
	protected static $defaultControllerActionName = 'view';
	/**
	 * The action params to use when calling defaultControllerActionName if no controller name can be gathered from the request.
	 *
	 * @var string
	 **/
	protected static $defaultControllerActionParams = array('page_name' => 'home');
	/**
	 * The default action name to call on a controller if the controller name
	 * was gathered from the request, but the action name couldn't be.
	 *
	 * @var string
	 **/
	protected static $defaultActionName = 'index';
	
	// Configuration Methods
	
	public static function addControllerPath($path) {
		self::$controllerPaths[] = $path;
	}
	public static function setControllerSuffix($suffix) {
		self::$controllerSuffix = $suffix;
	}
	public static function addControllerViewPath($path) {
		self::$controllerViewPaths[] = $path;
	}
	public static function setControllerViewSuffix($suffix) {
		self::$controllerViewSuffix = $suffix;
	}
	public static function addLayoutViewPath($path) {
		self::$layoutViewPaths[] = $path;
	}
	public static function setLayoutViewSuffix($suffix) {
		self::$layoutViewSuffix = $suffix;
	}
	public static function addElementViewPath($path) {
		self::$elementViewPaths[] = $path;
	}
	public static function setElementViewSuffix($suffix) {
		self::$elementViewSuffix = $suffix;
	}
	public static function setViewClassName($className) {
		self::$viewClassName = $className;
	}
	public static function setLayoutContentVarName($varName) {
		self::$layoutContentVarName = $varName;
	}
	public static function getLayoutContentVarName() {
		return self::$layoutContentVarName;
	}
	public static function setSendActionParamsAsArray($bool) {
		self::$sendActionParamsAsArray = $bool;
	}
	public static function getSendActionParamsAsArray() {
		return self::$sendActionParamsAsArray;
	}
	public static function setDefaultControllerName($defaultControllerName) {
		self::$defaultControllerName = $defaultControllerName;
	}
	public static function setDefaultControllerActionName($defaultControllerActionName) {
		self::$defaultControllerActionName = $defaultControllerActionName;
	}
	public static function setDefaultControllerActionParams($defaultControllerActionParams) {
		self::$defaultControllerActionParams = $defaultControllerActionParams;
	}
	public static function setDefaultActionName($defaultActionName) {
		self::$defaultActionName = $defaultActionName;
	}
	public static function getDefaultControllerName() {
		return self::$defaultControllerName;
	}
	public static function getDefaultControllerActionName() {
		return self::$defaultControllerActionName;
	}
	public static function getDefaultControllerActionParams() {
		return self::$defaultControllerActionParams;
	}
	public static function getDefaultActionName() {
		return self::$defaultActionName;
	}
	
	// Retrieval Methods
	
	public static function getController($controllerName, $controllerSubPath = "") {
		foreach (self::$controllerPaths as $path) {
			$file = $path . $controllerSubPath . $controllerName . self::$controllerSuffix;
			
			if (file_exists($file)) {
				include_once($file);
				$controllerClass = self::getControllerClassName($controllerName);
				$controller = new $controllerClass();
				$controller->setControllerName($controllerName);
				$controller->setControllerSubPath($controllerSubPath);
				return $controller;
			}
		}
		return null;
	}
	
	public static function getControllerClassName($controllerName) {
		return str_replace(' ', '', ucwords(str_replace(array('_', '/'), ' ', $controllerName))) . 'Controller';
	}
	
	public static function getActionFunctionName($actionName) {
		return 'action' . str_replace(' ', '', ucwords(str_replace('_', ' ', $actionName)));
	}
	
	public static function getControllerView($viewName, &$data = array()) {
		return self::getView($viewName, $data, self::$controllerViewPaths, self::$controllerViewSuffix);
	}
	
	public static function getElementView($elementName, &$data = array()) {
		return self::getView($elementName, $data, self::$elementViewPaths, self::$elementViewSuffix);
	}
	
	public static function getLayoutView($layoutName, &$data = array()) {
		return self::getView($layoutName, $data, self::$layoutViewPaths, self::$layoutViewSuffix);
	}
	
	/**
	 * As an Lvc developer, you'll probably want to use `getControllerView`,
	 * `getElementView`, or `getLayoutView`.
	 * 
	 * Example usage:
	 * 
	 *     // Pass the whole file name and leave off the last parameters
	 *     getView('/full/path/to/file/file.php', $data);
	 * 
	 *     // Pass the view name and specify the paths to scan and the suffix to append.
	 *     getView('file', $data, array('/full/path/to/file/'), '.php');
	 *
	 * @var mixed Lvc_View object if one is found, otherwise null.
	 * @see getControllerView(), getElementView(), getLayoutView(), Lvc_Config::setViewClassName()
	 **/
	public static function getView($viewName, &$data = array(), &$paths = array(''), $suffix = '') {
		foreach ($paths as $path) {
			$file = $path . $viewName . $suffix;
			if (file_exists($file)) {
				return new self::$viewClassName($file, $data);
			}
		}
		return null;
	}
	
	public static function dump() {
		echo '<pre>';
		
		echo '<strong>Controller Paths:</strong>' . "\n";
		print_r(self::$controllerPaths);
		echo '<strong>Controller Suffix:</strong> ' . self::$controllerSuffix . "\n\n";
		
		echo '<strong>Layout View Paths:</strong>' . "\n";
		print_r(self::$layoutViewPaths);
		echo '<strong>Layout View Suffix:</strong> '     . self::$layoutViewSuffix . "\n\n";
		
		echo '<strong>Controller View Paths:</strong>' . "\n";
		print_r(self::$controllerViewPaths);
		echo '<strong>Controller View Suffix:</strong> '    . self::$controllerViewSuffix . "\n\n";
		
		echo '<strong>Element View Paths:</strong>' . "\n";
		print_r(self::$elementViewPaths);
		echo '<strong>Element View Suffix:</strong> '    . self::$elementViewSuffix . "\n\n";
		
		echo '</pre>';
	}
}

/**
 * Lvc classes throw this type of exception.
 *
 * @package lightvc
 * @author Anthony Bush
 * @since 2007-04-20
 **/
class Lvc_Exception extends Exception {
	
}

/**
 * A request provides information about what controller and action to run and
 * what parameters to run them with.
 * 
 * @package lightvc
 * @author Anthony Bush
 * @since 2007-04-20
 **/
class Lvc_Request {
	protected $controllerName = '';
	protected $controllerSubPath = '';
	protected $controllerParams = array();
	protected $actionName = '';
	protected $actionParams = array();
	
	public function getControllerName() {
		return $this->controllerName;
	}
	public function getControllerSubPath() {
		return $this->controllerSubPath;
	}
	public function &getControllerParams() {
		return $this->controllerParams;
	}
	public function getActionName() {
		return $this->actionName;
	}
	public function &getActionParams() {
		return $this->actionParams;
	}

	public function setControllerName($controllerName) {
		$this->controllerName = trim($controllerName);
	}
	public function setControllerSubPath($controllerSubPath) {
		$this->controllerSubPath = trim($controllerSubPath);
	}
	public function setControllerParams(&$controllerParams) {
		$this->controllerParams = $controllerParams;
	}
	public function setActionName($actionName) {
		$this->actionName = trim($actionName);
	}
	public function setActionParams($actionParams) {
		$this->actionParams = $actionParams;
	}
	
	/**
	 * Override this in sub request objects to have custom error messages appended to
	 * LightVC messages.  For example, when HTTP Requests error, it might be useful
	 * to put the requested URL in the error log with the "Unable to load controller"
	 * message.
	 *
	 * @return string
	 * @since 2008-03-14
	 **/
	public function getAdditionalErrorInfo() {
		return '';
	}
}

/**
 * An HTTP request contains parameters from the GET, POST, PUT, and
 * DELETE arena.
 *
 * @package lightvc
 * @author Anthony Bush
 * @since 2007-04-20
 **/
class Lvc_HttpRequest extends Lvc_Request {
	protected $params = array();
	
	public function __construct() {
		
		$params = array();
		
		// Save GET data
		if (isset($_GET)) {
			$params['get'] =& $_GET;
		} else {
			$params['get'] = array();
		}
		
		// Ensure that we have a REQUEST_URI.
		if (isset($_SERVER['REQUEST_URI'])) {
			$params['uri'] = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
		} else {
			$params['uri'] = '';
		}
		
		// Save POST data
		$params['post'] =& $_POST;
		
		// Set params that will be used by routers.
		$this->setParams($params);
		// An HTTP request will default to passing all the parameters to the controller.
		$this->setControllerParams($params);
	}
	
	public function &getParams() {
		return $this->params;
	}
	public function setParams(&$params) {
		$this->params = $params;
	}
	
	/**
	 * Provides additional error information that might be useful when debugging
	 * errors.
	 *
	 * @return string
	 * @since 2008-03-14
	 **/
	public function getAdditionalErrorInfo() {
		if (isset($_SERVER['REQUEST_URI'])) {
			return 'Request URL was ' . $_SERVER['REQUEST_URI'];
		} else {
			return parent::getAdditionalErrorInfo();
		}
	}
}

/**
 * A router interface must at least provide a route() function that takes a
 * request object.
 * 
 * @package lightvc
 * @author Anthony Bush
 * @since 2007-04-22
 **/
interface Lvc_RouterInterface {
	/**
	 * Set the appropriate controller, action, and action parameters to use on
	 * the request object and return true. If no appropriate controller info
	 * can be found, return false.
	 * 
	 * @param mixed $request A request object to route.
	 * @return boolean
	 * @author Anthony Bush
	 * @since 2007-04-22
	 **/
	public function route($request);
}

/**
 * Routes a request using only GET data.
 * 
 * You can change the default keys for controller and action detection using
 * {@link setControllerKey()} and {@link setActionKey()} respectively.
 *
 * @package lightvc
 * @author Anthony Bush
 * @since 2007-04-22
 **/
class Lvc_GetRouter implements Lvc_RouterInterface {
	protected $controllerKey = 'controller';
	protected $actionKey = 'action';
	protected $actionParamsKey = null;
	protected $routes = array();
	
	public function setControllerKey($controllerKey) {
		$this->controllerKey = $controllerKey;
	}
	public function setActionKey($actionKey) {
		$this->actionKey = $actionKey;
	}
	public function setActionParamsKey($actionParamsKey) {
		$this->actionParamsKey = $actionParamsKey;
	}
	
	/**
	 * Add a param order for a controller / action.
	 * 
	 * For example:
	 * 
	 *     $router->addRoute('pages', 'show_page', array('page_name'));
	 * 
	 * will route:
	 * 
	 *     ?controller=pages&action=show_page&page_name=about
	 * 
	 * to:
	 * 
	 *     PagesController::actionShowPage('about');
	 * 
	 * whereas without the route the controller would be invoked with:
	 * 
	 *     PagesController::actionShowPage();
	 * 
	 * and you'd have to access the page_name via $this->get['page_name'].
	 *
	 * @return void
	 * @author Anthony Bush
	 * @since 2007-05-10
	 **/
	public function addRoute($controllerName, $actionName, $actionParamsOrder = array()) {
		$this->routes[$controllerName][$actionName] = $actionParamsOrder;
	}
	
	/**
	 * Set all routes at once. Useful if you want to specify routes in a
	 * config file and then pass them to this router all at once. See
	 * {@link addRoute()} for routing specifications.
	 * 
	 * @return void
	 * @author Anthony Bush
	 * @since 2007-05-10
	 **/
	public function setRoutes(&$routes) {
		$this->routes = $routes;
	}
	
	/**
	 * Construct the router and set all routes at once. See {@link setRoutes()}
	 * for more info.
	 *
	 * @return void
	 * @author Anthony Bush
	 * @see setRoutes()
	 * @since 2007-05-10
	 **/
	public function __construct(&$routes = null) {
		if ( ! is_null($routes)) {
			$this->setRoutes($routes);
		}
	}
	
	/**
	 * Attempts to routes a request using only the GET data.
	 * 
	 * @param Lvc_HttpRequest $request A request object to route.
	 * @return boolean
	 * @author Anthony Bush
	 * @since 2007-04-22
	 **/
	public function route($request) {
		$params = $request->getParams();
		
		// Use GET parameters to set controller, action, and action params
		if (isset($params['get'][$this->controllerKey])) {
			
			$request->setControllerName($params['get'][$this->controllerKey]);
			
			if (isset($params['get'][$this->actionKey])) {
				$request->setActionName($params['get'][$this->actionKey]);
			} else {
				$request->setActionName(Lvc_Config::getDefaultActionName());
			}
			
			// Using paramsKey method?
			if ( ! is_null($this->actionParamsKey) && isset($params['get'][$this->actionParamsKey])) {
				$request->setActionParams($params['get'][$this->actionParamsKey]);
			}
			// Using routes?
			else if ( ! empty($this->routes)) {
				if (isset($this->routes[$request->getControllerName()])
				 && isset($this->routes[$request->getControllerName()][$request->getActionName()])
				) {
					$actionParams = array();
					foreach ($this->routes[$request->getControllerName()][$request->getActionName()] as $paramName) {
						$actionParams[$paramName] = @$params['get'][$paramName];
					}
					$request->setActionParams($actionParams);
				}
			}
			
			return true;
		} else {
			return false;
		}
	}
}

/**
 * Attempts to route a request using the value for the 'uri' param, which
 * should be set by the web server. Any additional "directories" are
 * used as parameters for the action (using numeric indexes). Any extra GET
 * data is also amended to the action parameters.
 * 
 * If you need the numeric indexes to map to specific parameter names, use
 * the {@link Lvc_ParamOrderRewriteRouter} instead.
 * 
 * @package lightvc
 * @author Anthony Bush
 * @since 2007-04-22
 **/
class Lvc_RewriteRouter implements Lvc_RouterInterface {
	/**
	 * Attempts to route a request using the value for the 'uri' param, which
	 * should be set by the web server. Any additional "directories" are
	 * used as parameters for the action (using numeric indexes). Any extra GET
	 * data is also amended to the action parameters.
	 * 
	 * @param Lvc_HttpRequest $request A request object to route.
	 * @return boolean
	 * @author Anthony Bush
	 * @since 2007-04-22
	 **/
	public function route($request) {
		$params = $request->getParams();
		
		if (isset($params['uri'])) {
			
			$url = explode('/', $params['uri']);
			$count = count($url);
			
			// Set controller, action, and some action params from the segmented URL.
			if ($count > 0) {
				$request->setControllerName($url[0]);
				
				$actionParams = array();
				if ($count > 1) {
					$request->setActionName($url[1]);
					if ($count > 2) {
						for ($i = 2; $i < $count; $i++) {
							if ( ! empty($url[$i])) {
								$actionParams[] = $url[$i];
							}
						}
					}
				}
				
				$request->setActionParams($actionParams);
				return true;
			}
		}
		return false;
	}
}

/**
 * Routes a request using REQUEST_URI data and regular expressions specified by
 * the LightVC user.
 * 
 * Specify routes using {@link addRoute()}.
 *
 * @package lightvc
 * @author Anthony Bush
 * @since 2007-05-08
 **/
class Lvc_RegexRewriteRouter implements Lvc_RouterInterface {
	protected $routes = array();
	
	/**
	 * Specify a regular expression and how it should be routed.
	 * 
	 * For example:
	 * 
	 *     $regexRouter->addRoute('|^wee/([^/]+)/?$|', array(
	 *         'controller' => 'hello_world',
	 *         'action' => 'index',
	 *         'action_params' => array(1, 'constant_value')
	 *     ));
	 * 
	 * would map "wee/anything" and "wee/anything/" to:
	 * 
	 *     HelloWorldController::actionIndex('anything', 'constant_value');
	 * 
	 * but would not map "wee/anything/anything_else".
	 * 
	 * The format of the $parsingInfo parameter is as follows:
	 * 
	 *     'controller' => a hard coded controller name or an integer specifying which match in the regex to use.
	 *     'action' => a hard coded action name or an integer specifying which match in the regex to use.
	 *     'action_params' => array(
	 *         a hard coded action value or an integer specifying which match in the regex to use,
	 *         repeat above line as needed,
	 *     ),
	 *     'additional_params' => a hard coded integer specifying which match in the regex to use for additional parameters. These will be exploded by "/" and added to the action params.
	 * 
	 * or
	 * 
	 *     'redirect' => a replacement string that will be used to redirect to.  You can have parts of the original url mapped into the new one (like IDs).  See http://www.php.net/manual/en/function.preg-replace.php's documentation for the replacement parameter.
	 * 
	 * You can specify as much or as little as you want in the $parsingInfo.
	 * That is, if you don't specify the controller name or action name, then
	 * the defaults will be used by the Lvc_FrontController.
	 * 
	 * @param $regex regular expression to match the REQUEST_URI with.
	 * @param $parsingInfo an array containing any custom routing info.
	 * @return void
	 * @author Anthony Bush
	 * @since 2007-05-08
	 **/
	public function addRoute($regex, $parsingInfo = array()) {
		$this->routes[$regex] = $parsingInfo;
	}
	
	/**
	 * Set all routes at once. Useful if you want to specify routes in a
	 * config file and then pass them to this router all at once. See
	 * {@link addRoute()} for routing specifications.
	 * 
	 * @return void
	 * @author Anthony Bush
	 * @since 2007-05-08
	 **/
	public function setRoutes(&$routes) {
		$this->routes = $routes;
	}
	
	/**
	 * Construct the router and set all routes at once. See {@link setRoutes()}
	 * for more info.
	 *
	 * @return void
	 * @author Anthony Bush
	 * @see setRoutes()
	 * @since 2007-05-09
	 **/
	public function __construct(&$routes = null) {
		if ( ! is_null($routes)) {
			$this->setRoutes($routes);
		}
	}
	
	/**
	 * Routes like {@link Lvc_RewriteRouter} does, with the additional check to
	 * routes for specifying custom routes based on regular expressions.
	 * 
	 * @param Lvc_HttpRequest $request A request object to route.
	 * @return boolean
	 * @author Anthony Bush
	 * @since 2007-05-08
	 **/
	public function route($request) {
		$params = $request->getParams();
		
		if (isset($params['uri'])) {
			
			$url = $params['uri'];
			
			$matches = array();
			foreach ($this->routes as $regex => $parsingInfo) {
				if (preg_match($regex, $url, $matches)) {
					
					// Check for redirect action first
					if (isset($parsingInfo['redirect'])) {
						$redirectUrl = preg_replace($regex, $parsingInfo['redirect'], $url);
						// Output any custom headers, e.g. "HTTP/1.1 301 Moved Permanently"
						if (isset($parsingInfo['headers']))
						{
							if (is_array($parsingInfo['headers']))
							{
								foreach ($parsingInfo['headers'] as $header)
								{
									header($header);
								}
							}
							else
							{
								header($parsingInfo['headers']);
							}
						}
						header('Location: ' . $redirectUrl);
						exit();
					}
					
					// Get controller name if available
					if (isset($parsingInfo['controller'])) {
						if (is_int($parsingInfo['controller'])) {
							// Get the controller name from the regex matches
							$request->setControllerName(@$matches[$parsingInfo['controller']]);
						} else {
							// Use the constant value
							$request->setControllerName($parsingInfo['controller']);
						}
					}
					
					if (isset($parsingInfo['sub_path'])) {
						if (is_int($parsingInfo['sub_path'])){
							$request->setControllerSubPath(@$matches[$parsingInfo['sub_path']]);
						} else {
							// Use the constant value
							$request->setControllerSubPath($parsingInfo['sub_path']);
						}
					}
					
					// Get action name if available
					if (isset($parsingInfo['action'])) {
						if (is_int($parsingInfo['action'])) {
							// Get the action from the regex matches
							$request->setActionName(@$matches[$parsingInfo['action']]);
						} else {
							// Use the constant value
							$request->setActionName($parsingInfo['action']);
						}
					}
					
					// Get action parameters
					$actionParams = array();
					if (isset($parsingInfo['action_params'])) {
						foreach ($parsingInfo['action_params'] as $key => $value) {
							if (is_int($value)) {
								// Get the value from the regex matches
								if (isset($matches[$value])) {
									$actionParams[$key] = $matches[$value];
								} else {
									$actionParams[$key] = null;
								}
							} else {
								// Use the constant value
								$actionParams[$key] = $value;
							}
						}
					}
					if (isset($parsingInfo['additional_params'])) {
						if (is_int($parsingInfo['additional_params'])) {
							// Get the value from the regex matches
							if (isset($matches[$parsingInfo['additional_params']])) {
								$actionParams = $actionParams + explode('/', $matches[$parsingInfo['additional_params']]);
							}
						}
					}
					
					
					$request->setActionParams($actionParams);
					return true;
				} // route matched
			} // loop through routes
		} // uri value set
		return false;
	}
}

/**
 * FrontController takes a Request object and invokes the appropriate controller
 * and action.
 * 
 * Example Usage:
 * 
 *     $fc = new Lvc_FrontController();
 *     $fc->addRouter(new Lvc_GetRouter());
 *     $fc->processRequest(new Lvc_HttpRequest());
 * 
 * @package lightvc
 * @author Anthony Bush
 * @since 2007-04-20
 **/
class Lvc_FrontController {
	protected $routers = array();
	
	/**
	 * Add a router to give it a chance to route the request.
	 * 
	 * The first router to return true to the {@link route()} call
	 * will be the last router called, so add them in the order you want them
	 * to run.
	 *
	 * @return void
	 * @author Anthony Bush
	 **/
	public function addRouter(Lvc_RouterInterface $router) {
		$this->routers[] = $router;
	}
	
	/**
	 * Processes the request data by instantiating the appropriate controller and
	 * running the appropriate action.
	 *
	 * @return void
	 * @throws Lvc_Exception
	 * @author Anthony Bush
	 **/
	public function processRequest(Lvc_Request $request) {
		try
		{
			// Give routers a chance to (re)-route the request.
			foreach ($this->routers as $router) {                            
				if ($router->route($request)) {
					break;
				}
			}

			// Determine if a sub path to the controller was provided
			$controllerSubPath = $request->getControllerSubPath();
			if (!empty($controllerSubPath)) {
				$controllerSubPath .= "/";
			}
			
			// If controller name or action name are not set, set them to default.
			$controllerName = $request->getControllerName();                         
			
			if (empty($controllerName)) {
				$controllerName = Lvc_Config::getDefaultControllerName();
				$actionName     = Lvc_Config::getDefaultControllerActionName();
				$actionParams = $request->getActionParams() + Lvc_Config::getDefaultControllerActionParams();
				$request->setActionParams($actionParams);
			} else {
				$actionName = $request->getActionName();
				if (empty($actionName)) {
					$actionName   = Lvc_Config::getDefaultActionName();
				}
			}
                        
                        print "contoller = " . $controllerName . ",    ";
                        print "action = " . $actionName . "<br/>";

			$controller = Lvc_Config::getController($controllerName, $controllerSubPath);
			if (is_null($controller)) {
				throw new Lvc_Exception('Unable to load controller "' . $controllerName . '"');
			}
			$controller->setControllerParams($request->getControllerParams());
			$controller->runAction($actionName, $request->getActionParams());
		}
		catch (Lvc_Exception $e)
		{
			// Catch exceptions and append additional error info if the request object has anything to say.
			$moreInfo = $request->getAdditionalErrorInfo();
			if (!empty($moreInfo)) {
				throw new Lvc_Exception($e->getMessage() . '. ' . $moreInfo);
			} else {
				throw $e;
			}
		}
	}
}

/**
 * The base class that all other PageControllers should extend. Depending on the setup,
 * you might want an AppController to extend this one, and then have all your controllers
 * extend your AppController.
 * 
 * @package lightvc
 * @author Anthony Bush
 * @todo Finish up documentation in here...
 * @since 2007-04-20
 **/
class Lvc_PageController {
	/**
	 * Params is typically a combination of:
	 *     _GET (stored in params['get'])
	 *     _POST (stored in params['post'])
	 *
	 * @var array
	 **/
	protected $params = array();
	
	/**
	 * Reference to post data (i.e. $this->params['post'])
	 * 
	 * @var array
	 **/
	protected $post = array();
	
	/**
	 * Reference to get data (i.e. $this->params['get'])
	 * 
	 * @var array
	 **/
	protected $get = array();
	
	/**
	 * Controller Name (e.g. controller_name, not ControllerNameController)
	 *
	 * @var string
	 **/
	protected $controllerName = null;
	
	/**
	 * Controller Subpath. (e.g., if filesystem has /controllers/reports/report.php, 
	 * value = "reports")
	 *
	 * @var string
	 **/
	protected $controllerSubPath = null;
	
	/**
	 * Action Name (e.g. action_name, not actionActionName)
	 *
	 * @var string
	 **/
	protected $actionName = null;
	
	/**
	 * Variables we will pass to the view.
	 *
	 * @var array()
	 **/
	protected $viewVars = array();
	
	/**
	 * Have we loaded the view yet?
	 *
	 * @var boolean
	 **/
	protected $hasLoadedView = false;
	
	/**
	 * Specifies whether or not to load the default view for the action. If the
	 * action should not render any view, set it to false in the sub controller.
	 *
	 * @var boolean
	 **/
	protected $loadDefaultView = true;
	
	/**
	 * Don't set this yourself. It's used internally by parent controller /
	 * actions to determine whether or not to use the layout value in
	 * $layoutOverride rather than in $layout when requesting a sub action.
	 * 
	 * @var string
	 * @see setLayoutOverride(), $layoutOverride
	 **/
	protected $useLayoutOverride = false;
	
	/**
	 * Don't set this yourself. It's used internally by parent controller /
	 * actions to determine which layout to use when requesting a sub action.
	 * 
	 * @var string
	 * @see setLayoutOverride(), $useLayoutOverride
	 **/
	protected $layoutOverride = null;
	
	/**
	 * Set this in your controller to use a layout.
	 *
	 * @var string
	 **/
	protected $layout = null;
	
	/**
	 * An array of view variables specifically for the layout file.
	 *
	 * @var array
	 **/
	protected $layoutVars = array();
	
	/**
	 * Set the parameters of the controller.
	 * Actions will get their parameters through params['get'].
	 * Actions can access the post data as needed.
	 * 
	 * @param array $params an array of [paramName] => [paramValue] pairs
	 * @return void
	 * @author Anthony Bush
	 **/
	public function setControllerParams(&$params) {
		$this->params = $params;
		// Make a reference to the form data so we can get to it easier.
		if (isset($this->params['post'])) {
			$this->post =& $this->params['post'];
		}
		if (isset($this->params['get'])) {
			$this->get =& $this->params['get'];
		}
	}
	
	/**
	 * Don't call this yourself. It's used internally when creating new
	 * controllers so the controllers are aware of their name without
	 * needing any help from a user setting a member variable or from some
	 * reflector class.
	 *
	 * @return void
	 * @author Anthony Bush
	 **/
	public function setControllerName($controllerName) {
		$this->controllerName = $controllerName;
	}
	
	/**
	 * Don't call this yourself. It's used internally when creating new
	 * controllers so the controllers are aware of their sub path without
	 * needing any help from a user setting a member variable or from some
	 * reflector class.
	 *
	 * @return void
	 * @author Travis K. Jansen
	 **/
	public function setControllerSubPath($controllerSubPath) {
		$this->controllerSubPath = $controllerSubPath;
	}
	
	/**
	 * Set a variable for the view to use.
	 * 
	 * @param string $varName variable name to make available in the view
	 * @param $value value of the variable.
	 * @return void
	 * @author Anthony Bush
	 **/
	public function setVar($varName, $value) {
		$this->viewVars[$varName] = $value;
	}
	
	/**
	 * Set variables for the view in masse.
	 * 
	 * @param $varArray an array of [varName] => [value] pairs.
	 * @return void
	 * @author Anthony Bush
	 **/
	public function setVars(&$varArray) {
		$this->viewVars = $varArray + $this->viewVars;
	}
	
	/**
	 * Get the current value for a view variable.
	 * 
	 * @param string $varName
	 * @return mixed
	 * @author Anthony Bush
	 * @since 2007-11-13
	 **/
	public function getVar($varName) {
		if (isset($this->viewVars[$varName])) {
			return $this->viewVars[$varName];
		} else {
			return null;
		}
	}
	
	/**
	 * Set a variable for the layout view.
	 *
	 * @param $varName variable name to make available in the view
	 * @param $value value of the variable.
	 * @return void
	 * @author Anthony Bush
	 * @since 2007-05-17
	 **/
	public function setLayoutVar($varName, $value) {
		$this->layoutVars[$varName] = $value;
	}
	
	/**
	 * Get the current value for a layout variable.
	 * 
	 * @param string $varName
	 * @return mixed
	 * @author Anthony Bush
	 * @since 2007-11-13
	 **/
	public function getLayoutVar($varName) {
		if (isset($this->layoutVars[$varName])) {
			return $this->layoutVars[$varName];
		} else {
			return null;
		}
	}	
	
	/**
	 * Set the layout to use for the view.
	 * 
	 * @return void
	 * @author Anthony Bush
	 **/
	public function setLayout($layout) {
		$this->layout = $layout;
	}
	
	/**
	 * Don't call this yourself. It's used internally when requesting sub
	 * actions in order to avoid loading the layout multiple times.
	 *
	 * @return void
	 * @see $useLayoutOverride, $layoutOverride
	 * @author Anthony Bush
	 **/
	public function setLayoutOverride($layout) {
		$this->useLayoutOverride = true;
		$this->layoutOverride = $layout;
	}
	
	/**
	 * Returns the action name of this controller
	 *
	 * @return string
	 * @author lzhang
	 **/
	public function getActionName()
	{
		return $this->actionName;
	}
	
	/**
	 * Determine whether or not the the controller has the specified action.
	 * 
	 * @param string $actionName the action name to check for.
	 * @return boolean
	 * @author Anthony Bush
	 **/
	public function hasAction($actionName) {
		if (method_exists($this, Lvc_Config::getActionFunctionName($actionName))) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Runs the requested action and returns the output from it.
	 * 
	 * Typically called by the FrontController.
	 * 
	 * @param string $actionName the action name to run.
	 * @param array $actionParams the parameters to pass to the action.
	 * @return string output from running the action.
	 * @author Anthony Bush
	 **/
	public function getActionOutput($actionName, &$actionParams = array()) {
		ob_start();
		$this->runAction($actionName, $actionParams);
		return ob_get_clean();
	}
	
	/**
	 * Runs the requested action and outputs its results.
	 * 
	 * Typically called by the FrontController.
	 * 
	 * @param string $actionName the action name to run.
	 * @param array $actionParams the parameters to pass to the action.
	 * @return void
	 * @throws Lvc_Exception
	 * @author Anthony Bush
	 **/
	public function runAction($actionName, &$actionParams = array()) {
		$this->actionName = $actionName;
		$func = Lvc_Config::getActionFunctionName($actionName);
		if (method_exists($this, $func)) {
			$this->beforeAction();
			
			// Call the action
			if (Lvc_Config::getSendActionParamsAsArray()) {
				$this->$func($actionParams);
			} else {
				call_user_func_array(array($this, $func), $actionParams);
			}
			
			// Load the view
			if ( ! $this->hasLoadedView && $this->loadDefaultView) {
				$this->loadView($this->getControllerPath() . '/' . $actionName);
			}
			
			$this->afterAction();
			return true;
		} else {
			throw new Lvc_Exception('No action `' . $actionName . '`. Write the `' . $func . '` method');
		}
	}
	
	/**
	 * Load the requested controller view.
	 * 
	 * For example, you can load another view in your controller with:
	 * 
	 *     $this->loadView($this->getControllerPath() . '/some_other_action');
	 * 
	 * Or some other controller with:
	 *
	 *     $this->loadView('some_other_controller/some_other_action');
	 * 
	 * Remember, the view for your action will be rendered automatically.
	 * 
	 * @param string $controllerViewName 'controller_name/action_name' format.
	 * @return void
	 * @throws Lvc_Exception
	 * @author Anthony Bush
	 **/
	protected function loadView($controllerViewName) {
		
		$view = Lvc_Config::getControllerView($controllerViewName, $this->viewVars);
		if (is_null($view)) {
			throw new Lvc_Exception('Unable to load controller view "' . $controllerViewName . '" for controller "' . $this->controllerName . '"');
		} else {
			$view->setController($this);
			$viewContents = $view->getOutput();
		}
		
		if ($this->useLayoutOverride) {
			$this->layout = $this->layoutOverride;
		}
		if ( ! empty($this->layout)) {
			// Use an explicit name for this data so we don't override some other variable...
			$this->layoutVars[Lvc_Config::getLayoutContentVarName()] = $viewContents;
			$layoutView = Lvc_Config::getLayoutView($this->layout, $this->layoutVars);
			if (is_null($layoutView)) {
				throw new Lvc_Exception('Unable to load layout view "' . $this->layout . '" for controller "' . $this->controllerName . '"');
			} else {
				$layoutView->setController($this);
				$layoutView->output();
			}
		} else {
			echo($viewContents);
		}
		$this->hasLoadedView = true;
	}
	
	/**
	 * Redirect to the specified url. NOTE that this function stops execution.
	 * 
	 * @param string $url URL to redirect to.
	 * @return void
	 * @author Anthony Bush
	 **/
	protected function redirect($url) {
		header('Location: ' . $url);
		$this->afterAction();
		exit();
	}
	
	/**
	 * Execute code before every action.
	 * Override this in sub classes
	 *
	 * @return void
	 * @author Anthony Bush
	 **/
	protected function beforeAction() {
		
	}
	
	/**
	 * Execute code after every action.
	 * Override this in sub classes
	 *
	 * @return void
	 * @author Anthony Bush
	 **/
	protected function afterAction() {
		
	}
	
	/**
	 * Use this inside a controller action to get the output from another
	 * controller's action. By default, the layout functionality will be
	 * disabled for this "sub" action.
	 * 
	 * Example Usage:
	 * 
	 *     $enrollmentVerifyBox = $this->requestAction('enrollment_verify', array(), 'eligibility');
	 * 
	 * @param string $actionName name of action to invoke.
	 * @param array $actionParams parameters to invoke the action with.
	 * @param string $controllerName optional controller name. Current controller will be used if not specified.
	 * @param array $controllerParams optional controller params. Current controller params will be passed on if not specified.
	 * @param string $layout optional layout to force for the sub action.
	 * @return string output from requested controller's action.
	 * @throws Lvc_Exception
	 * @author Anthony Bush
	 **/
	protected function requestAction($actionName, $actionParams = array(), $controllerName = null, $controllerParams = null, $layout = null) {
		if (empty($controllerName)) {
			$controllerName = $this->controllerName;
		}
		if (is_null($controllerParams)) {
			$controllerParams = $this->params;
		}
		$controller = Lvc_Config::getController($controllerName);
		if (is_null($controller)) {
			throw new Lvc_Exception('Unable to load controller "' . $controllerName . '"');
		}
		$controller->setControllerParams($controllerParams);
		$controller->setLayoutOverride($layout);
		return $controller->getActionOutput($actionName, $actionParams);
	}
	
	/**
	 * Get the controller name. Mostly used internally...
	 *
	 * @return string controller name
	 * @author Anthony Bush
	 **/
	public function getControllerName() {
		return $this->controllerName;
	}
	
	/**
	 * Get the controller sub path. Mostly used internally...
	 *
	 * @return string controller sub path
	 * @author Travis K. Jansen
	 **/
	public function getControllerSubPath() {
		return $this->controllerSubPath;
	}
	
	/**
	 * Get the controller path (sub path + controller name). Mostly used internally...
	 *
	 * @return string controller path
	 * @author Travis K. Jansen
	 **/
	public function getControllerPath() {
		return $this->controllerSubPath . $this->controllerName;
	}
	
	/**
	 * Get the controller params. Mostly used internally...
	 *
	 * @return array controller params
	 * @author Anthony Bush
	 **/
	public function getControllerParams() {
		return $this->params;
	}
}

/**
 * A View can be outputted or have its output returned (i.e. it's renderable).
 * It can not be executed.
 * 
 * $inc = new Lvc_View('foo.php', array());
 * $inc->output();
 * $output = $inc->getOutput();
 * 
 * @package lightvc
 * @author Anthony Bush
 * @since 2007-04-20
 **/
class Lvc_View {
	/**
	 * Full path to file name to be included.
	 *
	 * @var string
	 **/
	protected $fileName;
	
	/**
	 * Data to be exposed to the view template file.
	 *
	 * @var array
	 **/
	protected $data;
	
	/**
	 * A reference to the parent controller
	 *
	 * @var Lvc_Controller
	 **/
	protected $controller;
	
	/**
	 * Construct a view to be rendered.
	 *
	 * @param string $fileName Full path to file name of the view template file.
	 * @param array $data an array of [varName] => [value] pairs. Each varName will be made available to the view.
	 * @return void
	 * @author Anthony Bush
	 **/
	public function __construct($fileName, &$data) {
		$this->fileName = $fileName;
		$this->data = $data;
	}
	
	/**
	 * Output the view (aka render).
	 *
	 * @return void
	 * @author Anthony Bush
	 **/
	public function output() {
		extract($this->data, EXTR_SKIP);
		include($this->fileName);
	}
	
	/**
	 * Return the output of the view.
	 *
	 * @return string output of view
	 * @author Anthony Bush
	 **/
	public function getOutput() {
		ob_start();
		$this->output();
		return ob_get_clean();
	}
	
	/**
	 * Render a sub element from within a view.
	 * 
	 * Views are not allowed to have business logic, but they can call upon
	 * other generic, shared, views, called elements here.
	 * 
	 * @param string $elementName name of element to render
	 * @param array $data optional data to pass to the element.
	 * @return void
	 * @throws Lvc_Exception
	 * @author Anthony Bush
	 **/
	protected function renderElement($elementName, $data = array()) {
		$view = Lvc_Config::getElementView($elementName, $data);
		if (!is_null($view)) {
			$view->setController($this->controller);
			$view->output();
		} else {
			error_log('Unable to render element "' . $elementName . '"');
			// throw new Lvc_Exception('Unable to render element "' . $elementName . '"');
		}
	}
	
	/**
	 * Set the controller when constructing a view if you want {@link setLayoutVar()}
	 * to be callable from a view.
	 *
	 * @return void
	 * @author Anthony Bush
	 * @since 2007-05-17
	 **/
	public function setController($controller) {
		$this->controller = $controller;
	}
	
	/**
	 * Set a variable for the layout file.  You can set the page title from a static
	 * page's view file this way.
	 * 
	 * @param $varName variable name to make available in the view
	 * @param $value value of the variable.
	 * @return void
	 * @author Anthony Bush
	 * @since 2007-05-17
	 **/
	public function setLayoutVar($varName, $value) {
		$this->controller->setLayoutVar($varName, $value);
	}
}

?>
