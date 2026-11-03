<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Includes\Admin;

/**
 * Admin Notice Class
 *
 * On the plugin's own pages only the plugin's own notices show: notices of other plugins, themes and WordPress
 * itself are taken off there. Every other admin page is left alone.
 *
 * @since 2.10.1
 */
class Notice {

	/**
	 * The plugin's pages (admin.php?page=...). Freemius pages (Account, Pricing) start with
	 * "oxi-image-hover-ultimate-" and are matched by that prefix.
	 *
	 * @since 9.12.3
	 * @var string[]
	 */
	const PAGES = [
		'oxi-image-hover-ultimate',
		'oxi-image-hover-shortcode',
		'image-hover-ultimate-getting-started',
		'oxi-image-hover-ultimate-settings',
	];

	/**
	 * The notice hooks WordPress prints in the admin header.
	 *
	 * @since 9.12.3
	 * @var string[]
	 */
	const HOOKS = [ 'admin_notices', 'all_admin_notices', 'network_admin_notices', 'user_admin_notices' ];

	public function __construct() {
		// in_admin_header runs right before WordPress prints the notice hooks.
		add_action( 'in_admin_header', [ $this, 'keep_own_notices' ], PHP_INT_MAX );
	}

	/**
	 * Whether the current admin screen is one of the plugin's pages.
	 *
	 * @since 9.12.3
	 * @return bool
	 */
	public function is_plugin_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- only reads which page is open.
		$page = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		return in_array( $page, self::PAGES, true ) || 0 === strpos( $page, 'oxi-image-hover-ultimate-' );
	}

	/**
	 * Takes every notice callback that isn't the plugin's own off the notice hooks, on the plugin's pages only.
	 *
	 * @since 9.12.3
	 * @return void
	 */
	public function keep_own_notices() {
		global $wp_filter;
		if ( ! $this->is_plugin_page() ) {
			return;
		}
		foreach ( self::HOOKS as $hook ) {
			if ( empty( $wp_filter[ $hook ] ) || ! $wp_filter[ $hook ] instanceof \WP_Hook ) {
				continue;
			}
			foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
				foreach ( $callbacks as $callback ) {
					if ( ! $this->is_own( $callback['function'] ) ) {
						remove_action( $hook, $callback['function'], $priority );
					}
				}
			}
		}
	}

	/**
	 * Whether a notice callback belongs to this plugin: a method of a plugin class (review, upgrade and recommended
	 * notices), or the Freemius notice manager of this plugin (license and account notices).
	 *
	 * @since 9.12.3
	 * @param mixed $callback Hooked callback.
	 * @return bool
	 */
	public function is_own( $callback ) {
		if ( ! is_array( $callback ) || ! isset( $callback[0] ) ) {
			return false;
		}
		$class = is_object( $callback[0] ) ? get_class( $callback[0] ) : (string) $callback[0];
		if ( 0 === strpos( ltrim( $class, '\\' ), 'OXI_IMAGE_HOVER_PLUGINS\\' ) ) {
			return true;
		}
		if ( class_exists( '\FS_Admin_Notice_Manager', false ) && $callback[0] instanceof \FS_Admin_Notice_Manager ) {
			// The manager's id is the Freemius slug of the plugin it belongs to.
			$id = \Closure::bind(
				function () {
					return $this->_id;
				},
				$callback[0],
				'FS_Admin_Notice_Manager'
			);
			return 'oxi-image-hover-ultimate' === $id();
		}
		return false;
	}
}
