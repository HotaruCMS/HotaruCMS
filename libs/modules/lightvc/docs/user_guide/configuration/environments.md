Environments
============

Environment switching features are not the scope of an MVC (maybe a full framework though).

However, here are some suggestions:

1. Never commit credentials to your repo.
2. Do commit an example config that can be copied and modified for each environment's needs (dev, production, etc.)

	For example, provide a file in `app/config/environment.example.php` that setups up a `$config` array and include `app/config/environment.php` in `app/config/application.php` (like how routes.php is included)
