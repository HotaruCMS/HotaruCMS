<?php

// Format of regex => parseInfo
$regexRoutes = array(
	
	// Map nothing to the home page.
	'#^$#' => array(
		'controller' => 'page',
		'action' => 'view',
		'action_params' => array(
			'page_name' => 'home',
		),
	),
	
	// Allow direct access to all pages via a "/page/page_name" URL.
	'#^page/(.*)$#' => array(
		'controller' => 'page',
		'action' => 'view',
		'action_params' => array(
			'page_name' => 1,
		),
	),
    
        // Allow direct access to all docs via a "/docs/doc_name" URL.
        '#^docs/(.*)$#' => array(
                'controller' => 'docs',
                'action' => 'view',
                'action_params' => array(
                        'doc_name' => 1,
                ),
        ),
    
        // Allow certain controllers to divert to view as default if no action given.
        '#^([^/]+)/(.*)$#' => array(
                'controller' => 1,
                'action' => 'view',
                'action_params' => array(
                        'name' => 2,
                ),
        ),
	
	// Map controler/action/params
	'#^([^/]+)/([^/]+)/?(.*)$#' => array(
		'controller' => 1,
		'action' => 2,
		'additional_params' => 3,
	),
	
	// Map controllers to a default action (not needed if you use the
	// Lvc_Config static setters for default controller name, action
	// name, and action params.)
	'#^([^/]+)/?$#' => array(
		'controller' => 1,
		'action' => 'index',
	),
	
);

?>