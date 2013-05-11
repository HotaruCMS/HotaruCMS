<?php

class CategoryController extends AppController
{
	public function actionView($name = 'home')
	{
//		if (strpos($pageName, '../') !== false)
//		{
//			throw new Lvc_Exception('File Not Found: ' . $sourceFile);
//		}
		
                print "**category controller for " . $name;
		//$this->loadView('page/' . rtrim($pageName, '/'));
	}
        
        public function actionTest()
	{
		if (strpos($pageName, '../') !== false)
		{
			throw new Lvc_Exception('File Not Found: ' . $sourceFile);
		}
		
                print "**category controller test";
		//$this->loadView('page/' . rtrim($pageName, '/'));
	}
}

?>