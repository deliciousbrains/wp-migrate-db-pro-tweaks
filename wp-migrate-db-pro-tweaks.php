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
		//add_filter( 'admin_menu', array( $this, 'remove_menu_item' ) );
		//add_filter( 'wpmdb_domain_replaces', array( $this, 'add_additional_domain_replaces' ) );
		//add_filter( 'wpmdb_pre_recursive_unserialize_replace', array( $this, 'pre_recursive_unserialize_replace' ), 10, 3 );
		//add_filter( 'wpmdb_before_replace_custom_data', array( $this, 'before_replace_custom_data' ), 10, 2 );
		//add_filter( 'wpmdb_replace_custom_data', array( $this, 'replace_custom_data' ), 10, 2 );
		//add_filter( 'wpmdb_after_replace_custom_data', array( $this, 'after_replace_custom_data' ), 10, 3 );
		//add_filter( 'wpmdb_abort_utf8mb4_to_utf8', array( $this, 'abort_utf8mb4_to_utf8' ) );
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
	 * We display a warning on the WP Migrate DB migration form the current environment
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
	 * with SQL constraints. You may alter the name of this table using this filter.
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

	/**
	 * Removes the WP Migrate DB Pro menu item from Tools -> Migrate DB Pro
	 * The page is still accessible if you directly navigate to http://your-website.com/wp-admin/tools.php?page=wp-migrate-db-pro
	*/
	function remove_menu_item(){
		remove_submenu_page( 'tools.php', 'wp-migrate-db-pro' );
	}

	/**
	 * Only useful for those wanting to update domain mapped subsites in a multisite installation
	 * Must return an array in the following format:
	 * array(
	 * 		'regex pattern' => 'replace value'
	 * );
	 * See actual example of this in the code below
	*/
	function add_additional_domain_replaces( $replaces ) {
		$replaces['/bananas.dev/'] = 'bananas.com';
		$replaces['/apples.dev/'] = 'apples.com';
		return $replaces;
	}

	/**
	 * Allows developers to hijack the find/replace process allowing them to massage database field values during a migration.
	 *
	 * Returning anything other than boolean false in this function will short-circuit the find/replace process and
	 * instead use the data returned by this function.
	 *
	 * The hooked function will run across every field value in the database, ensure that the code is optimized for
	 * speed. CPU and file I/O intensive code will massively slow down the migration.
	 *
	 * @param  mixed  $pre           Either boolean false or the massaged data from another hooked function.
	 * @param  mixed  $data          A specific database field value.
	 * @param  object $wpmdb_replace An instance of the WPMDB_Replace class.
	 * @return mixed                 Either boolean false or the massaged data.
	 */
	function pre_recursive_unserialize_replace( $pre, $data, $wpmdb_replace ) {
		// This data has already been processed by another hooked function, do not process it again.
		if ( false !== $pre ) {
			return $pre;
		}

		// Do not process an empty field value.
		if ( empty( $data ) ) {
			return $pre;
		}

		// Only process data from a certain table in our database.
		if ( false === $wpmdb_replace->table_is( 'options' ) ) {
			return $pre;
		}

		$row = $wpmdb_replace->get_row();

		// Only process data from a certain row in our database. e.g. an option in the wp_options table
		if ( ! isset( $row->option_name ) || 'custom_data' !== $row->option_name ) {
			return $pre;
		}

		// Only process data from a certain column in our database.
		if ( 'option_value' !== $wpmdb_replace->get_column() ) {
			return $pre;
		}

		// Perform some arbitrary action
		$data = rtrim( $data, 'string to remove from end of data' );

		return $data;
	}

	/**
	 * Allows developers to massage database string field values during a migration.
	 *
	 * Similar to the wpmdb_pre_recursive_unserialize_replace function but occurs later and only triggers once the data
	 * has been simplified down to a string data type. As such the returned massaged data must also be a string.
	 *
	 * The hooked function will run across every field value in the database, ensure that the code is optimized for
	 * speed. CPU and file I/O intensive code will massively slow down the migration.
	 *
	 * The example below anonymizes email addresses.
	 *
	 * @param  array  $args          An array containing a database string field value and a boolean value.
	 * @param  object $wpmdb_replace An instance of the WPMDB_Replace class.
	 * @return array                 An array containing the massaged string field value and a boolean value.
	 */
	function replace_custom_data( $args, $wpmdb_replace ) {
		// This data has already been processed by another hooked function, do not process it again.
		if ( false === $args[1] ) {
			return $args;
		}

		// Replaces all instances of email addresses to example@example.com to protect against email harvesters.
		// You probably want something more meaningful here.
		if ( is_email( $args[0] ) ) {
			// do the replacement only if it is a push (not pull)
			if ( 'push' == $wpmdb_replace->get_intent() ) {
				$args[0] = 'example@example.com';
				$args[1] = false; // False here signifies that we wish to prevent any further processing of this field value.
			}
		}

		return $args;
	}

	/**
	 * Allows developers to massage database field values before performing the recursive find and replace.
	 *
	 * The hooked function can run across every field value in the database, ensure that the code is optimized for
	 * speed. CPU and file I/O intensive code will massively slow down the migration.
	 *
	 * The below example decodes base64 data for use in Muffin Builder prior to performing the find and replace.
	 *
	 * @param  array  $args          An array containing a database field value, a boolean indicating if before fired
	 *                               and a boolean value indicating whether to fire this action recursively.
	 * @param  object $wpmdb_replace An instance of the WPMDB_Replace class.
	 *
	 * @return array                 An array containing the massaged string field value and a boolean value.
	 */
	function before_replace_custom_data( $args, $wpmdb_replace ) {
		// Only process data from a certain table in our database.
		if ( false === $wpmdb_replace->table_is( 'postmeta' ) ) {
			return $args;
		}

		$row = $wpmdb_replace->get_row();

		// Only process data from a certain row in our database. e.g. an option in the wp_options table
		if ( ! isset( $row->meta_key ) || 'mfn-page-items' !== $row->meta_key ) {
			return $args;
		}

		// Only process data from a certain column in our database.
		if ( 'meta_value' !== $wpmdb_replace->get_column() ) {
			return $args;
		}

		// Ensure data is a string
		if ( ! is_string( $args[0] ) ) {
			return $args;
		}

		// Decode the data
		if ( $decoded = base64_decode( trim( $args[0] ), true ) ) {
			// Processed data
			$args[0] = $decoded;
			// True here informs the `wpmdb_after_replace_custom_data` filter that `wpmdb_before_replace_custom_data` has fired.
			// This allows you to fire the before and after filters in pairs (see below method).
			$args[1] = true;
			// False here signifies that we don't want to perform this filter recursively.
			$args[2] = false;
		}

		return $args;
	}

	/**
	 * Allows developers to massage database field values after performing the recursive find and replace.
	 *
	 * The hooked function can run across every field value in the database, ensure that the code is optimized for
	 * speed. CPU and file I/O intensive code will massively slow down the migration.
	 *
	 * The below example encodes base64 data for use in Muffin Builder after performing the find and replace.
	 *
	 * @param  array  $args          An array containing a database field value, a boolean indicating if before fired
	 *                               and a boolean value indicating whether to fire this action recursively.
	 * @param  object $wpmdb_replace An instance of the WPMDB_Replace class.
	 *
	 * @return string                A string containing the data.
	 */
	function after_replace_custom_data( $data, $before_fired, $wpmdb_replace ) {
		// Only process if before fired
		if ( false === $before_fired ) {
			return $data;
		}

		// Only process data from a certain table in our database.
		if ( false === $wpmdb_replace->table_is( 'postmeta' ) ) {
			return $data;
		}

		$row = $wpmdb_replace->get_row();

		// Only process data from a certain row in our database. e.g. an option in the wp_options table
		if ( ! isset( $row->meta_key ) || 'mfn-page-items' !== $row->meta_key ) {
			return $data;
		}

		// Only process data from a certain column in our database.
		if ( 'meta_value' !== $wpmdb_replace->get_column() ) {
			return $data;
		}

		// Ensure data is a string
		if ( ! is_string( $data ) ) {
			return $data;
		}

		// Re-encode the data
		$data = base64_encode( $data );

		return $data;
	}

	/**
	 * By default WP Migrate DB Pro will abort an attempt to go from utf8mb4 to utf8 in case of data loss.
	 *
	 * Return false for the wpmdb_abort_utf8mb4_to_utf8 filter to override this.
	 *
	 * @see https://deliciousbrains.com/wp-migrate-db-pro/doc/source-site-supports-utf8mb4/
	 *
	 * @param bool $abort_utf8mb4_to_utf8
	 *
	 * @return bool
	 */
	function abort_utf8mb4_to_utf8( $abort_utf8mb4_to_utf8 ) {
		return false;
	}
}

new WP_Migrate_DB_Pro_Tweaks();
