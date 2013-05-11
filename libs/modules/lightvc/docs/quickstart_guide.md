Quickstart Guide
================

Installation
------------

1. Make sure your environment meets the requirements (PHP5).
2. [Download LightVC](https://github.com/awbush/lightvc/tags).
3. Unzip/extract the download.
4. Point the web server's document root to the webroot folder.  Check out the [web server config examples](user_guide/configuration/web_server.md) if you need help here.

Start Building
--------------

1. Add a controller and action (`controllers/test_lightvc.php`):

		<?php
		class TestLightvcController extends AppController {
			public function actionTestAction($one = null, $two = null) {
				if (is_null($one)) {
					$one = 'NULL';
				}
				if (is_null($two)) {
					$two = 'NULL';
				}
				$this->setVar('one', $one);
				$this->setVar('two', $two);
			}
		}
		?>

2. Add a view (`views/test_lightvc/test_action.php`):

		<h1>Test LightVC</h1>
		<p>One = "<?php echo htmlentities($one) ?>" and two = "<?php echo htmlentities($two) ?>."</p>

3. Visit `/test_lightvc/test_action/` on your server.
