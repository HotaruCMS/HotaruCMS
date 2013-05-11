<?php

class SortController extends AppController
{
	public function actionView($name = 'home')
	{
//		if (strpos($pageName, '../') !== false)
//		{
//			throw new Lvc_Exception('File Not Found: ' . $sourceFile);
//		}
		
                print "**Sort controller for " . $name;
		//$this->loadView('page/' . rtrim($pageName, '/'));
	}
        
        public function actionTest()
	{
		if (strpos($pageName, '../') !== false)
		{
			throw new Lvc_Exception('File Not Found: ' . $sourceFile);
		}
		
                print "**Sort controller test";
		//$this->loadView('page/' . rtrim($pageName, '/'));
	}
}

?>