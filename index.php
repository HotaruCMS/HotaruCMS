<?php

// includes
require_once('hotaru_header.php');

// Include "main" language file
if (file_exists(LANGUAGES . LANGUAGE_PACK . 'main_language.php'))
{
    // language file from the chosen language pack
    include_once(LANGUAGES . LANGUAGE_PACK . 'main_language.php');
}
else 
{
   // try the default language pack
    require_once(LANGUAGES . 'language_default/main_language.php'); 
}

// Include combined css and js files
if ($cage->get->keyExists('combine')) {
    $type = $cage->get->testAlpha('type');
    $version = $cage->get->testInt('version');
    $hotaru->combineIncludes($type, $version);
}

$hotaru->displayTemplate('index');
?>