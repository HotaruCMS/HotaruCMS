<?php
require_once "Paginated.php";
require_once "DoubleBarLayout.php";
?>
<html>
<head>
<title>Pagination</title>

<!-- Just a little style formatting. Has no bearing on example -->
<style type="text/css">
	body {
		font-family: Verdana;
		font-size: 13px;
	}
	
	a {
		text-decoration: none;
	}
	
	a:hover {
		text-decoration: underline;
	}
</style>
<!-- End style formatting -->
</head>

<body>

	<?php
	//create an array of names in alphabetic order. A database call could have retrieved these items
	$names = array("Andrew", "Bernard", "Castello", "Dennis", "Ernie", "Frank", "Greg", "Henry", "Isac", "Jax", "Kester", "Leonard", "Matthew", "Nigel", "Oscar");
	
	$page = $_GET['page'];
	
	//constructor takes three parameters
	//1. array to be paged
	//2. number of results per page (optional parameter. Default is 10)
	//3. the current page (optional parameter. Default  is 1)
	$pagedResults = new Paginated($names, 10, $page);
	
	echo "<ul>";

	while($row = $pagedResults->fetchPagedRow()) {	//when $row is false loop terminates
		echo "<li>{$row}</li>";
	}
	
	echo "</ul>";
	
	//important to set the strategy to be used before a call to fetchPagedNavigation
	$pagedResults->setLayout(new DoubleBarLayout());
	echo $pagedResults->fetchPagedNavigation();
	?>
</body>
</html>