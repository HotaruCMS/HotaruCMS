<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv=Content-Type content='text/html; charset=UTF-8'>

	<!-- Title -->
	<title><?php echo $lang['install_title']; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="text">
	<link rel="stylesheet" type="text/css" href="../libs/frameworks/bootstrap/css/bootstrap.min.css">	
	<link rel="stylesheet" type="text/css" href="../libs/frameworks/bootstrap/css/bootstrap-responsive.min.css">
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>	
	<link href="css/install_style.css" rel="stylesheet">        		
</head>

	<!-- Body -->
	<body>
	<div class="navbar navbar-inverse navbar-static-top">
            <div class="navbar-inner">
                    <div class="container">
                            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                            </button>
                            <a class="brand" href="index.php"><?php echo $lang['admin_theme_header_hotarucms']; ?></a>
                            <ul class="nav">
                                    <li><a href="index.php?step=1&action=install"><?php echo $lang['install_new2']; ?></a></li>
                                    <li class="active"><a href="index.php?step=1&action=upgrade"><?php echo $lang['install_upgrade2']; ?></a></li>
                                    <li><a href="../templates/instruction.html">Help</a></li>
                                    <li><a href="../index.php">Home</a></li>
                            </ul>
                    </div>
            </div>
	</div><br />
	
	<div class="container">