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
		//add_filter( 'wpmdb_upload_info', array( $this, 'upload_info' ) );
		//add_filter( 'wpmdb_upload_dir_name', array( $this, 'upload_dir_name' ) );
		//add_filter( 'wpmdb_default_remote_post_timeout', array( $this, 'default_remote_post_timeout' ) );
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

	/**
	 * This action fires after a migration has been successfully completed.
	 * It will fire on both the local and remote machines.
	 * In this example, we send an email to the DBA once a migration has completed.
	*/
	function migration_complete( $migration_type, $connection_url ) {
		if( $migration_type == 'push' ) {
			wp_mail( 
				'dba@yourwebsite.com', 
				'Push migration complete!', 
				sprintf( 'Hi there, we just pushed the DB from %s to %s, this occured at %s.', home_url(), $connection_url, current_time( 'mysql' ) ) 
			);
		}
		else {
			wp_mail( 
				'dba@yourwebsite.com', 
				'Pull migration complete!', 
				sprintf( 'Hi there, we just pulled the DB from %s into %s, this occured at %s.', $connection_url, home_url(), current_time( 'mysql' ) ) 
			);
		}
	}

	/**
	 * Custom file upload directory and URL
	 * If using the "Export" or "Backup" features in WP Migrate DB Pro we will need to write files to your filesystem.
	 * This filter allows you to define a custom folder to write to.
	*/
	function upload_info() {
		// The returned data needs to be in a very specific format, see below for example
		return array(
			'path' 	=> '/path/to/custom/uploads/directory', // <- note missing end trailing slash
			'url'	=> 'http://yourwebsite.com/custom/uploads/directory' // <- note missing end trailing slash
		);
	}

	/**
	 * Custom upload directory name
	 * If you decide not to use the above filter you can instead define a custom directory name here.
	 * The default name is 'wp-migrate-db' and the default path is 'wp-content/uploads/wp-migrate-db'.
	 * Please use standard folder naming conventions, no spaces, no underscores, no caps, no special characters, etc
	 * Note: you cannot use this filter if you're already using the filter above, it will be ignored.
	*/
	function upload_dir_name() {
		return 'database-dumps';
	}

	/**
	 * Used within our remote_post() function
	 * Defines a timeout that is used when making HTTP POST requests to the remote server.
	 * Is used when requesting SQL from the server or when transferring SQL to a remote server.
	 * Value in seconds.
	 * Default is 60 * 20 (20 minutes)
	*/
	function default_remote_post_timeout( $timeout ) {
		return 60 * 30;
	}

}

new WP_Migrate_DB_Pro_Tweaks();
