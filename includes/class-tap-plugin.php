<?php
defined( 'ABSPATH' ) || exit;
final class TAP_Plugin {
	public static function boot(): void {
		add_action( 'init', array( __CLASS__, 'register_caps' ) );
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}
	public static function register_caps(): void {
		$role = get_role( 'administrator' );
		if ( $role ) {
			foreach ( array( 'tap_manage_access', 'tap_manage_tasks', 'tap_view_live_monitor', 'tap_manage_approvals' ) as $cap ) { $role->add_cap( $cap ); }
		}
	}
	public static function register_routes(): void {
		register_rest_route( 'tap/v1', '/heartbeat', array(
			'methods' => 'POST',
			'permission_callback' => static function () { return is_user_logged_in(); },
			'callback' => array( __CLASS__, 'heartbeat' ),
		) );
		register_rest_route( 'tap/v1', '/health', array(
			'methods' => 'GET',
			'permission_callback' => '__return_true',
			'callback' => static function () { return rest_ensure_response( array( 'ok' => true, 'version' => TAP_VERSION ) ); },
		) );
	}
	public static function heartbeat( WP_REST_Request $request ): WP_REST_Response {
		$session = sanitize_text_field( (string) $request->get_param( 'session_key' ) );
		if ( strlen( $session ) < 32 ) {
			return new WP_REST_Response( array( 'ok' => false, 'code' => 'invalid_session' ), 400 );
		}
		return new WP_REST_Response( array( 'ok' => true, 'state' => 'active', 'server_time' => time() ) );
	}
}
