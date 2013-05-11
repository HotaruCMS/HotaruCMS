<?php

class AppController extends Lvc_PageController
{
	protected $layout = 'default';
	
	protected function beforeAction()
	{
		$this->setLayoutVar('pageTitle', 'Untitled');
		$this->requireCss('master.css');
	}
	
	public function requireCss($cssFile)
	{
		$this->layoutVars['requiredCss'][$cssFile] = true;
	}
	
	public function requireJs($jsFile)
	{
		$this->layoutVars['requiredJs'][$jsFile] = true;
	}
	
	public function requireJsInHead($jsFile)
	{
		$this->layoutVars['requiredJsInHead'][$jsFile] = true;
	}
	
	protected function loadPageNotFound()
	{
		$this->sendHttpStatusHeader('404');
		$this->loadView('error/404');
	}
	
	public function sendHttpStatusHeader($code)
	{
		include_once('HttpStatusCode.class.php');
		$statusCode = new HttpStatusCode($code);
		header('HTTP 1.1 ' . $statusCode->getCode() . ' ' . $statusCode->getDefinition());
		return $statusCode;
	}
	
	public function redirectToAction($actionName)
	{
		$this->redirect(WWW_BASE_PATH . $this->getControllerPath() . '/' . $actionName);
	}
	
}

?>