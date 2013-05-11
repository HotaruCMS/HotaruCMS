<?php

class AppView extends Lvc_View
{
	public function requireCss($cssFile)
	{
		$this->controller->requireCss($cssFile);
	}
	
	public function requireJs($jsFile)
	{
		$this->controller->requireJs($jsFile);
	}
	
	public function requireJsInHead($jsFile)
	{
		$this->controller->requireJsInHead($jsFile);
	}
}

?>