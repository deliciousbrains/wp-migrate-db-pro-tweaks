WP Migrate DB Pro Tweaks
========================

This is a WordPress plugin, meant as a starting point for developers to tweak WP Migrate DB Pro using WordPress filters.

Installation
------------

Create a /wp-migrate-db-pro-tweaks/ folder in /wp-content/plugins/ and simply drop the wp-migrate-db-pro-tweaks.php file into it. Then go to the Plugins page in your WordPress dashboard and activate it.

Setup
-----

Open the wp-migrate-db-pro-tweaks.php file and take a look at the `init()` function. You will notice that all the calls to `add_filter()` are commented out. So, at the moment the plugin does nothing even though it's activated. To enable a filter, simply uncomment the appropriate `add_filter()` line.