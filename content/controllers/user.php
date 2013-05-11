<?php

class UserController extends AppController
{
	public function actionView($name = 'home')
	{
//		if (strpos($pageName, '../') !== false)
//		{
//			throw new Lvc_Exception('File Not Found: ' . $sourceFile);
//		}
		
                print "**User controller for " . $name;
		//$this->loadView('page/' . rtrim($pageName, '/'));
	}
        
        public function actionTest()
	{
		if (strpos($pageName, '../') !== false)
		{
			throw new Lvc_Exception('File Not Found: ' . $sourceFile);
		}
		
                print "**User controller test";
		//$this->loadView('page/' . rtrim($pageName, '/'));
	}
}

?>