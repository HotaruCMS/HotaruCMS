Configuration
=============

Most things in LightVC are customizable.  For example:

* Paths for controllers, views, elements, and layouts.
* File extensions.
* Whether or not to pass parameters to controller actions as function arguments or an array.
* Default controller/actions to use.

Customizing LightVC is easy, and is done through the `Lvc_Config` static methods.

Customizing Paths
-----------------

Many paths can be added for each part of the application by calling `addControllerPath()`, `addControllerViewPath()`, `addLayoutViewPath()`, and `addElementViewPath()`.

For example, the following could be placed in an application's `app/config/application.php` file to setup a single directory for each piece:

	<?php
	define('APP_PATH', dirname(dirname(__FILE__)));
	Lvc_Config::addControllerPath(APP_PATH . 'controllers/');
	Lvc_Config::addControllerViewPath(APP_PATH . 'views/');
	Lvc_Config::addLayoutViewPath(APP_PATH . 'views/layouts/');
	Lvc_Config::addElementViewPath(APP_PATH . 'views/elements/');
	?>

Customizing File Suffixes/Extensions
------------------------------------

The default suffix for all items is `.php`.  This can be changed as shown in the following example:

	<?php
	Lvc_Config::setControllerSuffix('_controller.php');
	Lvc_Config::setControllerViewSuffix('.thml');
	Lvc_Config::setLayoutViewSuffix('_layout.thml');
	Lvc_Config::setElementViewSuffix('_element.thml');
	?>

Customizing Action Parameter Passing
------------------------------------

This option is controlled through `setSendActionParamsAsArray()`, and defaults to `false`.  To change it, use:

	<?php
	Lvc_Config::setSendActionParamsAsArray(true);
	?>

When set to true, controller actions need to accept only one parameter, like so:

	<?php
	class ExampleController extends AppController {
		public function actionTest($params) {
		}
	}
	?>

The `$params` will contain an array of the arguments passed to the controller.

If left off (recommended if mod_rewrite is available), controller actions should be coded like so:

	<?php
	class ExampleController extends AppController {
		public function actionTest($paramOne = null, $paramTwo = null /*, and so on... */) {
		}
	}
	?>

Customizing Default Controller/Action
-------------------------------------

If all routes fail while processing a request, LightVC will try one last time using the defaults specified in `Lvc_Config`.  These can be customized like so:

	<?php
	// The controller name to use if no controller name can be gathered from the
	// request.
	Lvc_Config::setDefaultControllerName('page');
	
	// The action name to call on the defaultControllerName if no controller name can
	// be gathered from the request.
	Lvc_Config::setDefaultControllerActionName('view');
	
	// The action params to use when calling defaultControllerActionName if no
	// controller name can be gathered from the request.
	Lvc_Config::setDefaultControllerActionParams(array('page_name' => 'home'));
	?>

It's possible that the route was able to map a controller to use but not the action.  The default action to invoke can be specified like so:

	<?php
	// The default action name to call on a controller if the controller name was
	// gathered from the request, but the action name couldn't be.
	Lvc_Config::setDefaultActionName('index');
	?>

Use an AppController
--------------------

This requires no config calls to LightVC.  Just write an `AppController` class that extends `Lvc_PageController`:

	<?php
	class AppController extends Lvc_PageController {
		protected $layout = 'default';
	}
	?>

Then make all controllers extend `AppController` rather than `Lvc_PageController`.

Use an AppView
--------------

To add custom functionality to the View layer, write an `AppView` class that extends `Lvc_View`:

	<?php
	class AppView extends Lvc_View {
		public function requireCss($cssFile) {
			$this->controller->requireCss($cssFile);
		}
	}
	?>

Then tell LightVC about it:

	<?php
	Lvc_Config::setViewClassName('AppView');
	?>

Customizing Layout Variable Names
---------------------------------

The only layout variable hard-coded into LightVC is `layoutContent`, which contains the output from the controller's view.  Even this can be changed, as in the following example:

	<?php
	Lvc_Config::setLayoutContentVarName('content_for_layout');
	?>
