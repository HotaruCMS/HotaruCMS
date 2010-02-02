<?php 
/**
 * Theme name: Default Blues
 * Template name: footer.php
 * Template author: Jason F. Irwin
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
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>
</div>

<!-- FOOTER -->
<div id="footer">
	<p>Default Blues Theme Created by <a href='http://www.j2fi.net/' title='Jason Irwin dot Net'>Jason F. Irwin</a> | Site Powered By <a href='http://hotarucms.org/' title='HotaruCMS'>HotaruCMS</a></p>
	<p>Copyright <b>&copy;</b> <?php echo date("Y"); ?></p>
</div>
<!-- End Footer -->

<?php $h->pluginHook('pre_close_body'); ?>

</body>
</html>
