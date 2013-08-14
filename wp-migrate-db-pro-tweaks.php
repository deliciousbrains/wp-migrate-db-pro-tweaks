<?php
/*
Plugin Name: WP Migrate DB Pro Tweaks
Plugin URI: http://github.com/deliciousbrains/wp-migrate-db-pro-tweaks
Description: Examples of using WP Migrate DB Pro's filters
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

class WP_Migrate_DB_Pro_Tweaks {

    function __construct() {
        if ( !is_admin() ) return;
        add_action( 'init', array( $this, 'init' ), 9 );
    }

    function init() {
        // Uncomment the following lines to initiate a filter

        //add_filter( 'wpmdb_migration_complete', array( $this, 'migration_complete', 10, 2 );
        //add_filter( 'wpmdb_bottleneck', array( $this, 'bottleneck' ), 10, 2 );
        //add_filter( 'wpmdb_sensible_pull_limit', array( $this, 'sensible_pull_limit' ), 10, 2 );
        //add_filter( 'wpmdb_temporary_prefix', array( $this, 'temporary_prefix' ) );
        //add_filter( 'wpmdb_preserved_options', array( $this, 'preserved_options' ) );
    }

    // By default, 'wpmdb_settings' and 'wpmdb_error_log' are preserved
    // when the database is overwritten in a migration. This filter allows 
    // you to define additional options to preserve
    function preserved_options( $options ) {
        $options[] = 'blogname';
        return $options;
    }

    // Override the temporary table name prefix
    function temporary_prefix( $prefix ) {
        return 'mig_';
    }

    // Force bottleneck
    function bottleneck( $bytes ) {
        return 1024 * 1024; // 1MB
    }

    // Force pull limit
    function sensible_pull_limit( $bytes ) {
        return 1024 * 1024; // 1MB
    }

    // Counts migrations by connection URL and migration type
    function migration_complete( $migration_type, $connection_url ) {
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
}

new WP_Migrate_DB_Pro_Tweaks();
