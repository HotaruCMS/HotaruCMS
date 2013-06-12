<?php 
/**
 * File: /themes/404.php
 * Purpose: Last resort "Page not found" template, shown when the requested page is not found.
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>

<?php $msg1 = $h->lang('main_hotaru_site_closed'); ?>

<html lang="en"><head>
    <meta charset="utf-8">
    <title><?php echo SITE_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="title" content="Hotaru CMS">

    <!-- styles -->
    <link href="<?php echo SITEURL; ?>libs/frameworks/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
    <link href="<?php echo SITEURL; ?>libs/frameworks/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">

  </head>

  <body>

    <div class="container-narrow">

      <div class="masthead">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="<?php echo SITEURL; ?>">Home</a></li>  
          <li><a href="<?php echo SITEURL; ?>admin_index.php?page=admin_login">Admin</a></li>

        </ul>
        <h3 class="muted"><?php echo SITE_NAME; ?></h3>
      </div>

      <hr>

      <div class="jumbotron">
        <h1>Maintenance</h1>
        <p class="lead"><?php echo $msg1; ?></p>
<!--        <a class="btn btn-large btn-success" href="http://hotarucms.org">Hotaru CMS</a>-->

        <p>
            <br/>
        <center><img src="<?php echo SITEURL; ?>content/admin_themes/admin_default/images/hotaru-80px.png"></center>
      </p>
      </div>

      <hr>
     
      <div class="footer">
          <center>
            © <?php echo SITE_NAME; ?> 2013<br/>                             
          <a href="http://hotarucms.org"><img src="/content/admin_themes/admin_default/images/hotarucms.png" alt="Hotaru CMS"></a>
          </center>
         
      </div>

    </div> <!-- /container -->
