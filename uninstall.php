<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
global $wpdb;
foreach ( array( $wpdb->prefix . 'tap_events', $wpdb->prefix . 'tap_sessions', $wpdb->prefix . 'tap_grants', $wpdb->prefix . 'tap_tasks' ) as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}
delete_option( 'tap_db_version' );
