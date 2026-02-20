<?php
/**
 * Easy Delivery Uninstall
 *
 * Uninstalling Easy Delivery tables & option
 *
 * @version 1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

delete_option('edp_general_option');
/**
 * Delete Table
*/
$edp_table_name = "{$wpdb->prefix}edp_store_details"; //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$wpdb->query("DROP TABLE IF EXISTS `" .esc_sql($edp_table_name) . "`"); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
flush_rewrite_rules(false);