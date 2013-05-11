Web Server Configuration Examples
=================================

Nginx Example
-------------

Nginx with php-fpm could work with a vhost like this:

	server {
		root /path/to/lightvc-app/webroot;
		server_name local.domain.com;
		sendfile off;

		try_files $uri /index.php?$args;

		location ~ \.php$ {
			fastcgi_pass 127.0.0.1:9000;
			fastcgi_index index.php;
			include fastcgi_params;
		}
	}

Those contents could be in `/etc/nginx/sites-available/lightvc-app.conf` with a symlink in `/etc/nginx/sites-enabled/` pointing to it.  Depends on your setup.

### References ###

* [Nginx try_files documentation](http://wiki.nginx.org/NginxHttpCoreModule#try_files)

Apache (1 & 2) Example
----------------------

To enable the pretty URLs, the `.htaccess` file inside the `webroot` directory must be read by Apache.  You need only set `AllowOverride All` in the Apache configuration file like so:

	<Directory "/path/to/lightvc-app/webroot">
		AllowOverride All
	</Directory>

If you prefer to setup port-based virtual hosts (e.g. on a development machine), the following example sets up a port-based virtual host, with `.htacess` support, for port 8000.  This means if the site is setup on the local machine it can be accessed by visiting [http://localhost:8000/](http://localhost:8000/).

	Listen 8000
	NameVirtualHost *:8000
	<VirtualHost *:8000>
		DocumentRoot "/path/to/lightvc-app/webroot"
	</VirtualHost>
	<Directory "/path/to/lightvc-app/webroot">
		Options Indexes FollowSymLinks
		# Allow .htaccess files
		AllowOverride All
		Order allow,deny
		Allow from all
	</Directory>

**CAUTION**: The best practice is not to have any .htaccess usage at all.  It's provided as a convenience, but if you have full control over the apache conf files you should copy the contents into the `<Directory>` section.  See [when (not) to use .htaccess files](http://httpd.apache.org/docs/2.2/howto/htaccess.html#when).  For example, replace `AllowOverride All` in either of the above examples with:
	
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_METHOD} !OPTIONS
    RewriteRule ^(.*)$ index.php [L]

### References ###

* [Apache 2.2 AllowOverride Documentation](http://httpd.apache.org/docs/2.2/mod/core.html#allowoverride)

Lighttpd Example
----------------

Lighttpd will not read the `.htacess` file packaged with LightVC's skeleton app, so you'll have to add to the configuration file.  The tricky part is that you'll have to manually specify which files and directories are not supposed to be parsed by LightVC.  This is different from the Apache/`.htaccess` solution which automatically loads files that exist in the file system, otherwise control is passed to LightVC.

The following example shows how to ensure common files and folders like `favicon.ico`, `robots.txt`, `images`, `css`, and `js` work correctly in Lighttpd.

	# /etc/lighttpd/lighttpd.conf
	url.rewrite-once = (
		"^/(css|images|files|js)/(.*)$" => "/$1/$2",
		"^/(robots\.txt|favicon\.ico)$" => "/$1",
		"^/([^?]*)(\?(.*))?$" => "/index.php?url=$1&$3"
	)

### References ###

* [Lighttpd mod_rewrite Documentation](http://trac.lighttpd.net/trac/wiki/Docs%3AModRewrite)
