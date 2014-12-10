<?php 
    $urlLang = urlLang($h);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv=Content-Type content='text/html; charset=UTF-8'>

	<!-- Title -->
	<title><?php echo $lang['install_title']; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta http-equiv="Content-Type" content="text">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/bootstrap/3.3.1/css/bootstrap.min.css">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>	
	<link href="css/install_style.css" rel="stylesheet">
</head>

<!-- Body -->
<body>
	<nav class="navbar navbar-default navbar-static-top" role="navigation">
		<div class="navbar-inner">
			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.php"><?php echo $lang['admin_theme_header_hotarucms']; ?></a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active"><a href="index.php?step=1&action=install"><?php echo $lang['install_new2']; ?></a></li>
						<li class=""><a href="index.php?step=1&action=upgrade"><?php echo $lang['install_upgrade2']; ?></a></li>
						<li><a href="./templates/instruction.html">Help</a></li>
						<li><a href="/index.php">Site</a></li>
					</ul>

					<ul class="nav navbar-nav navbar-right">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo $currentLang['name']; ?><span class="caret"></span></a>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="<?php echo $urlLang . 'en'; ?>">English</a></li>
                                                        <li class="divider"></li>
                                                        <li><a href="<?php echo $urlLang . 'ta'; ?>">Tamil</a></li>
                                                        <li><a href="<?php echo $urlLang . 'tr'; ?>">Turkish</a></li>
                                                        <li><a href="<?php echo $urlLang . 'cs_CZ'; ?>">Czech</a></li>
                                                        <li><a href="<?php echo $urlLang . 'ja_JP'; ?>">日本語</a></li>
                                                        
                                                    </ul>
                                                </li>
                                                <li><a href="http://forums.hotarucms.org">Forums</a></li>
                                                <li><a href="http://forums.hotarucms.org/misc/contact">Contact</a></li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</div><!-- /.navbar-inner -->
	</nav>
	
	<div class="container">