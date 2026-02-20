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
$table_name = "{$wpdb->prefix}edp_store_details";
$wpdb->query("DROP TABLE IF EXISTS `" .esc_sql($table_name) . "`");
flush_rewrite_rules(false);