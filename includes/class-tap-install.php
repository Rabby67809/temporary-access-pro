<?php
defined( 'ABSPATH' ) || exit;
final class TAP_Install {
	public const DB_VERSION = 1;
	public static function activate(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset = $wpdb->get_charset_collate();
		$prefix  = $wpdb->prefix;
		$tables = array(
			"CREATE TABLE {$prefix}tap_tasks (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				owner_user_id bigint(20) unsigned NOT NULL,
				title varchar(190) NOT NULL,
				description longtext NULL,
				status varchar(20) NOT NULL DEFAULT 'draft',
				priority varchar(20) NOT NULL DEFAULT 'normal',
				progress tinyint unsigned NOT NULL DEFAULT 0,
				starts_at datetime NULL,
				expires_at datetime NULL,
				created_at datetime NOT NULL,
				updated_at datetime NOT NULL,
				PRIMARY KEY (id), KEY owner_user_id (owner_user_id), KEY status (status), KEY expires_at (expires_at)
			) {$charset};",
			"CREATE TABLE {$prefix}tap_grants (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				task_id bigint(20) unsigned NOT NULL,
				worker_user_id bigint(20) unsigned NOT NULL,
				policy_json longtext NOT NULL,
				status varchar(20) NOT NULL DEFAULT 'active',
				token_hash char(64) NULL,
				starts_at datetime NULL,
				expires_at datetime NULL,
				created_at datetime NOT NULL,
				updated_at datetime NOT NULL,
				PRIMARY KEY (id), KEY task_id (task_id), KEY worker_user_id (worker_user_id), KEY status (status), KEY expires_at (expires_at)
			) {$charset};",
			"CREATE TABLE {$prefix}tap_sessions (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				grant_id bigint(20) unsigned NOT NULL,
				session_key char(64) NOT NULL,
				state varchar(20) NOT NULL DEFAULT 'active',
				last_seen datetime NOT NULL,
				started_at datetime NOT NULL,
				ended_at datetime NULL,
				ip_hash char(64) NULL,
				device_hash char(64) NULL,
				PRIMARY KEY (id), UNIQUE KEY session_key (session_key), KEY grant_id (grant_id), KEY state (state), KEY last_seen (last_seen)
			) {$charset};",
			"CREATE TABLE {$prefix}tap_events (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				task_id bigint(20) unsigned NULL,
				grant_id bigint(20) unsigned NULL,
				session_id bigint(20) unsigned NULL,
				actor_user_id bigint(20) unsigned NULL,
				event_type varchar(80) NOT NULL,
				object_type varchar(80) NULL,
				object_id bigint(20) unsigned NULL,
				payload_json longtext NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY (id), KEY task_id (task_id), KEY grant_id (grant_id), KEY session_id (session_id), KEY actor_user_id (actor_user_id), KEY event_type (event_type), KEY created_at (created_at)
			) {$charset};",
		);
		foreach ( $tables as $sql ) { dbDelta( $sql ); }
		update_option( 'tap_db_version', self::DB_VERSION, false );
	}
	public static function maybe_upgrade(): void {
		if ( (int) get_option( 'tap_db_version', 0 ) < self::DB_VERSION ) { self::activate(); }
	}
	public static function deactivate(): void { wp_clear_scheduled_hook( 'tap_session_cleanup' ); }
}
