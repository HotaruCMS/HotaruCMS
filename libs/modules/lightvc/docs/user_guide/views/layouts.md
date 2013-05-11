Layouts
=======

Layouts are special views that get wrapped around the controller's view output.  They don't have to be used, but are the best way to make several pages use the same "layout."

Layouts are really just [views](../views.md).

An example layout might contain:

	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title><?php echo htmlentities($pageTitle); ?></title>
	</head>
	<body>
		<?php echo $layoutContent; ?>
	</body>
	</html>

Where `$layoutContent` will be set to the output from a controller action's view.

Which layout gets used is specified in the [controller](../controllers.md#changing_the_layout).
