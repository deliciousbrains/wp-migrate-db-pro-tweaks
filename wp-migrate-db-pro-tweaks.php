<?php
/*
Plugin Name: WP Migrate DB Pro Tweaks
Plugin URI: http://deliciousbrains.com/wp-migrate-db-pro/
Description: Tweaks using WP Migrate DB Pro's filters
Author: Delicious Brains
Version: 0.1
Author URI: http://deliciousbrains.com
*/

// Copyright (c) 2013 Delicious Brains. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************

if ( version_compare( PHP_VERSION, '5.2', '<' ) ) {
    // Thanks for this Yoast!
	if ( is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) ) {
		require_once ABSPATH.'/wp-admin/includes/plugin.php';
		deactivate_plugins( __FILE__ );
	    wp_die( __('WP Migrate DB Pro requires PHP 5.2 or higher, as does WordPress 3.2 and higher. The plugin has now disabled itself.', 'wp-migrate-db' ) );
	}
}

function wpmdbpro_tweaks_admin_init() {
    add_filter( 'wpmdb_migration_complete', 'wpmdbpro_tweaks_migration_complete', 10, 2 );
}

add_action( 'admin_init', 'wpmdbpro_tweaks_admin_init' );

// Counts migrations by connection URL and migration type
function wpmdbpro_tweaks_migration_complete( $migration_type, $connection_url ) {
    $slug = 'wpmdbpro_stats';
    
    $stats = get_option( $slug );
    if ( !$stats ) {
        $stats = array();
    }

    if ( isset( $stats[$connection_url][$migration_type] ) ) {
        $stats[$connection_url][$migration_type] = $stats[$connection_url][$migration_type] + 1;
    }
    else {
        $stats[$connection_url][$migration_type] = 1;
    }
    
    update_option( $slug, $stats );
}