<?php

/**
 * Includes settings and constructs Hotaru.
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
// includes
if(file_exists('config/settings.php') ) {
	require_once('config/settings.php');
	require_once('Hotaru.php');
	$h = new Hotaru();
        $h->start('main');
} else {
        	
	if(file_exists('install/index.php') ) {
            $msg1 = 'Hotaru is having trouble starting.<br/>You may need to install the system before you can proceed further.<br/><br/>';		
	} else {
            $msg1 = 'Hotaru is having trouble starting.<br/>The install files need to be downloaded before you can proceed further.<br/><br/>';
	}
}
?>


<html lang="en"><head>
    <meta charset="utf-8">
    <title>Hotaru CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- styles -->
    <link href="libs/frameworks/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 40px;
      }

      /* Custom container */
      .container-narrow {
        margin: 0 auto;
        max-width: 700px;
      }
      .container-narrow > hr {
        margin: 30px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 60px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 72px;
        line-height: 1;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }
      .marketing p + h4 {
        margin-top: 28px;
      }
    </style>
    <link href="libs/frameworks/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">

  </head>

  <body>

    <div class="container-narrow">

      <div class="masthead">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="/">Home</a></li>
          <li><a href="http://forums.hotarucms.org">Forums</a></li>
          <li><a href="http://hotarucms.org">Contact</a></li>
        </ul>
        <h3 class="muted">Hotaru CMS</h3>
      </div>

      <hr>

      <div class="jumbotron">
        <h1>Oops!</h1>
        <p class="lead"><?php echo $msg1; ?></p>
        <a class="btn btn-large btn-success" href="http://hotarucms.org">Hotaru CMS</a>
      </div>

      <hr>

      <div class="row-fluid marketing">
        <div class="span6">
          <h4>Hotaru CMS</h4>
          <p>Hotaru CMS allows you to create great bookmarking, journal and other social web sites. Be your own site admin and create your own social network niche.</p>

          <h4>Social</h4>
          <p>Built for sharing information, Hotaru CMS lets you add content easily and share it with other social networks.</p>
        </div>

        <div class="span6">
          <h4>Bookmarking</h4>
          <p>Although flexible to be used for different purposes, Hotaru CMS is a leader in creating social bookmarking sites. </p>

          <h4>Forums</h4>
          <p>There is always plenty of help in the forums from developers and site owners</p>
        </div>
      </div>

      <hr>

      <div class="footer">
        <p>© Hotaru CMS 2013</p>
      </div>

    </div> <!-- /container -->
