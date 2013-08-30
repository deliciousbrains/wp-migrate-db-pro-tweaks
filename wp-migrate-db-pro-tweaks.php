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
		// Uncomment the following lines to initiate an action / filter

		//add_action( 'wpmdb_migration_complete', array( $this, 'migration_complete' ), 10, 2 );
		//add_filter( 'wpmdb_bottleneck', array( $this, 'bottleneck' ) );
		//add_filter( 'wpmdb_sensible_pull_limit', array( $this, 'sensible_pull_limit' ) );
		//add_filter( 'wpmdb_temporary_prefix', array( $this, 'temporary_prefix' ) );
		//add_filter( 'wpmdb_upload_info', array( $this, 'upload_info' ) );
		//add_filter( 'wpmdb_upload_dir_name', array( $this, 'upload_dir_name' ) );
		//add_filter( 'wpmdb_default_remote_post_timeout', array( $this, 'default_remote_post_timeout' ) );
		//add_filter( 'wpmdb_preserved_options', array( $this, 'preserved_options' ) );
		//add_filter( 'wpmdb_hide_safe_mode_warning', array( $this, 'hide_safe_mode_warning' ) );
		//add_filter( 'wpmdb_create_table_query', array( $this, 'create_table_query' ), 10, 2 );
		//add_filter( 'wpmdb_rows_where', array( $this, 'rows_where' ), 10, 2 );
		//add_filter( 'wpmdb_rows_per_segment', array( $this, 'rows_per_segment' ) );
		//add_filter( 'wpmdb_alter_table_name', array( $this, 'alter_table_name' ) );
		//add_filter( 'wpmdb_prepare_remote_connection_timeout', array( $this, 'prepare_remote_connection_timeout' ) );
	}

	/**
	* By default, 'wpmdb_settings' and 'wpmdb_error_log' are preserved when the database is overwritten in a migration.
	* This filter allows you to define additional options (from wp_options) to preserve during a migration.
	* The example below preserves the 'blogname' value though any number of additional options may be added.
	*/
	function preserved_options( $options ) {
		$options[] = 'blogname';
		return $options;
	}

	/**
	 * When migrating tables we assign them a temporary prefix so that they don't directly override existing tables 
	 * on the remote website. Once all the tables have been migrated we drop the existing tables and rename the 
	 * tables with the temporary prefix to their original names. e.g. _mig_wp_options becomes wp_options
	 * This filter allows you to alter that temporary prefix.
	 * The default is _mig_
	*/
	function temporary_prefix( $prefix ) {
		return '_m_';
	}

	/**
	 * This filter defines the absolute max upper limit size of a POST request body.
	 * Reduce this value if you're running into memory or other environmental server issues.
	 * This will only effect push migrations.
	 * Value in bytes.
	 * The default is determined by your post_max_size and a few other variables.
	*/
	function bottleneck( $bytes ) {
		return 1024 * 1024; // 1MB
	}

	/**
	 * This filter defines the absolute max upper limit size of a request body.
	 * Reduce this value if you're running into memory or other environmental server issues.
	 * This will only effect pull migrations.
	 * Value in bytes.
	 * The default is 26214400 bytes (25mb).
	*/
	function sensible_pull_limit( $bytes ) {
		return 1024 * 1024; // 1MB
	}

	/**
	 * This action fires after a migration has been successfully completed.
	 * It will fire on both the local and remote machines.
	 * In this example, we send an email to the DBA once a migration has completed.
	*/
	function migration_complete( $migration_type, $connection_url ) {
		$email = 'dba@yourwebsite.com';
		$subject = sprintf( '%s migration complete!', ucfirst( $migration_type ) );
		
		if ( 'push' == $migration_type ) {		
			$migration_from = home_url();
			$migration_to = $connection_url;
		}
		else {
			$migration_from = $connection_url;
			$migration_to = home_url();
		}
		
		$body = sprintf( 'Hi there, we just %sed the DB from %s to %s, this occured at %s.', 
			$migration_type, $migration_from, $migration_to, current_time( 'mysql' ) );

		wp_mail( $email, $subject, $body );
	}

	/**
	 * Custom file upload directory and URL
	 * If using the "Export" or "Backup" features in WP Migrate DB Pro we will need to write files to your filesystem.
	 * This filter allows you to define a custom folder to write to.
	*/
	function upload_info() {
		// The returned data needs to be in a very specific format, see below for example
		return array(
			'path' 	=> '/path/to/custom/uploads/directory', // note missing end trailing slash
			'url'	=> 'http://yourwebsite.com/custom/uploads/directory' // note missing end trailing slash
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

	/**
	 * We display a warning on the WP Migrate DB migration form the the current environment
	 * has PHP's safe mode enabled. To dismiss this warning, simply return true in hooked function.
	 * Default is false.
	*/
	function hide_safe_mode_warning() {
		return true;
	}

	/**
	 * Alters the CREATE TABLE SQL query
	 * There might be a certain circumstance where you need your tables to be created differently than the default method.
	 * We use the SHOW CREATE TABLE query to determine the SQL that is required to create the WordPress tables.
	 * The example below demonstrates an engine change.
	*/
	function create_table_query( $create_table_query, $table ) {
		return str_ireplace( 'ENGINE=aria', 'ENGINE=InnoDB', $create_table_query );
	}

	/**
	 * Alter the WHERE clause when selecting data to migrate
	 * Using this filter you can exclude certain data from the migration.
	 * You must first check if the $where variable is empty and alter your return value accordingly.
	 * The example below excludes the admin user from being migrated to the remote site.
	*/
	function rows_where( $where, $table ) {
		global $wpdb;
		if( $wpdb->prefix . 'users' != $table ) return $where;
		$where .= ( empty( $where ) ? 'WHERE ' : ' AND ' );
		$where .= "`user_login` NOT LIKE 'admin'";
		return $where;
	}

	/**
	 * Alters the number of table rows stored in memory while the table is being processed.
	 * Reduce this number if you're running into memory problems.
	 * Default is 100
	*/
	function rows_per_segment( $rows ) {
		return 50;
	}

	/**
	 * We create a special table that stores ALTER queries during the migration.
	 * This allows us to run these queries at the very end of the migration to prevent issues
	 * with SQL contraints. You may alter the name of this table using this filter.
	 * Default is wp_wpmdb_alter_statements
	 *
	*/
	function alter_table_name( $table_name ) {
		global $wpdb;
		return $wpdb->prefix . 'alter_queries';
	}

	/**
	 * Defines a timeout that is used when making the first initial request to the remote server.
	 * It occurs when the user pastes the remote connection information into the local machines connection box.
	 * Value in seconds.
	 * Default is 10
	*/
	function prepare_remote_connection_timeout( $timeout ) {
		return 20;
	}

}

new WP_Migrate_DB_Pro_Tweaks();
