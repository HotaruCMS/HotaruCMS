<?php

class TagController extends AppController
{
	public function actionView($name = 'home')
	{
//		if (strpos($pageName, '../') !== false)
//		{
//			throw new Lvc_Exception('File Not Found: ' . $sourceFile);
//		}
		
                require_once('Hotaru.php');
                $h = new Hotaru();
                $h->start('mvc');
                $h->pageName = "all";
                print "**tag controller for " . $name . "<br/>";
                $this->setVar('h', $h);
		$this->loadView('../themes/default/index');
	}
        
        public function actionTest()
	{
		if (strpos($pageName, '../') !== false)
		{
			throw new Lvc_Exception('File Not Found: ' . $sourceFile);
		}
		
                print "**tag controller test";
		//$this->loadView('page/' . rtrim($pageName, '/'));
	}
}

?>