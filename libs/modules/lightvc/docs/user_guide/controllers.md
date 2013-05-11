Controllers
===========

* [Access Get/Post Data](#access_get_post_data)
* [Manually invoke (or disable) a view](#manually_invoke_view)
* [Changing/Disabling the Layout](#changing_the_layout)
* [Passing Variables to the View](#passing_variables_to_the_view)
* [Setting Layout Variables](#setting_layout_variables)
* [Redirecting](#redirecting)
* [Requesting a sub action](#requesting_a_sub_action)
* [Execute code before/after an action](#execute_code_before_after_an_action)

<a name="access_get_post_data"></a>
Access Get/Post Data
--------------------

In the controller:

	$exampleGet = $this->get['example'];
	$examplePost = $this->post['example'];

You may use the superglobals `$_GET` and `$_POST`, but using `$this->get` and `$this->post` will allow you to [make sub-requests](#requesting_a_sub_action).  For example, if you use `requestAction()` you can pass a get and post that don't necessarily match the superglobals, so you would want that controller/action to be using `$this->get` and `$this->post`.  In other words, your code will be more re-usable if it stays away from the superglobals.

<a name="manually_invoke_view"></a>
Manually invoke (or disable) a view
-----------------------------------

The view corresponding to the current action is automatically invoked by default.

You can force a view to load at any time (thus disabling the automatic invocation):

	$this->loadView($this->controllerName . '/custom_view');

You can also disable any view from loading:

	$this->loadDefaultView = false;

<a name="changing_the_layout"></a>
Changing/Disabling the Layout
-----------------------------

You can change the layout in the controller at any time with:

	$this->setLayout('new_layout');

You can disable it by setting it to null (the default):

	$this->setLayout(null);

It probably makes most sense to specify a default layout in an `AppController` and then override it on an as-needed basis:

	<?php
	class AppController extends Lvc_Controller {
		protected $layout = 'default';
	}
	?>

<a name="passing_variables_to_the_view"></a>
Passing Variables to the View
-----------------------------

In the controller:

	$this->setVar('message', 'Weeeee!');

In the view:

	<?php echo $message ?>

You can also build an array of variables and set them en masse:

	$data = array();
	$data['error'] = '';
	$data['message'] = 'Weeeeeeeeeeee!';
	$data['userName'] = 'Nobody';
	$this->setVars($data);

<a name="setting_layout_variables"></a>
Setting Layout Variables
------------------------

In the controller:

	$this->setLayoutVar('layoutVarName', 'value');

In the layout:

	<?php echo $layoutVarName ?>

<a name="redirecting"></a>
Redirecting
-----------

In the controller:

	$this->redirect($url); // redirect exits, so no code after this will be run

<a name="requesting_a_sub_action"></a>
Requesting a sub action
-----------------------

Inside a controller action method:

	$request = new Lvc_Request();
	$request->setControllerName($this->controllerName);
	$request->setControllerParams($this->params);
	$request->setActionName('some_other_action');
	$request->setActionParams($params); // Set optional params
	$output = $this->getRequestOutput($request);

Or, you can pass the request attributes to the `requestAction()` method:

	$output = $this->requestAction($actionName);
	$output = $this->requestAction($actionName, $actionParams, $controllerName, $controllerParams);

<a name="execute_code_before_after_an_action"></a>
Execute code before/after an action
-----------------------------------

To execute code *before* an action, override the `beforeAction()` method:

	protected function beforeAction() {
		parent::beforeAction(); // chain to parent
		$this->setLayoutVar('pageTitle', 'Default Title');
	}

To execute code *after* an action, override the `afterAction()` method:

	protected function afterAction() {
		parent::afterAction(); // chain to parent
		// do some stuff
	}
