<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Removes Image Hover Effects data from the database.
 *
 * Two entry points:
 * - delete_now(): the Danger zone button on the Settings page. The plugin
 *   stays active, so its tables are emptied, not dropped, and the licence is
 *   kept.
 * - uninstall(): runs from Freemius' after_uninstall action when the plugin
 *   is deleted, only on sites where "Delete data when the plugin is deleted"
 *   is switched on. Drops the plugin's own tables and removes every option.
 *
 * Shared with Flipbox (and older Oxilab plugins), so never removed outright:
 * - the {prefix}oxi_div_import table: only rows of type "{module}-ultimate",
 *   which only this plugin writes, are deleted;
 * - oxi_addons_font_awesome and oxi_addons_google_font: kept while another
 *   Oxilab plugin is installed, because Flipbox reads them too.
 *
 * Freemius keeps its own data and handles it on uninstall.
 *
 * @since 9.11.9
 */
class Data_Cleaner {

    /**
     * Option that turns on cleanup when the plugin is deleted.
     */
    const UNINSTALL_OPTION = 'oxi_image_hover_delete_data_on_uninstall';

    /**
     * Module types this plugin stores in the shared oxi_div_import table.
     */
    const MODULES = [ 'general', 'caption', 'flipbox', 'button', 'square', 'lightbox', 'comparison', 'magnifier', 'carousel', 'filter', 'display' ];

    /**
     * Settings page options that belong to this plugin only.
     */
    const SETTINGS = [
        'oxi_image_user_permission',
        'image_hover_ultimate_mobile_device_key',
        'oxi_addons_way_points',
        'oxi_addons_custom_parent_class',
        'oxi_image_support_massage',
    ];

    /**
     * Internal options (install state, notice dismissals, widget instances).
     */
    const OPTIONS = [
        'oxi_image_hover_version',
        'oxi_image_hover_activation_date',
        'oxi_image_hover_nobug',
        'oxi_image_hover_upgrade_date',
        'oxi_image_hover_upgrade_nobug',
        'oxi_image_hover_recommended',
        'image_hover_ultimate_update_complete',
        'widget_iheu_widget',
    ];

    /**
     * Options this plugin shares with other Oxilab plugins.
     */
    const SHARED_OPTIONS = [ 'oxi_addons_font_awesome', 'oxi_addons_google_font' ];

    /**
     * Legacy (pre Freemius) licence options.
     */
    const LICENSE_OPTIONS = [ 'image_hover_ultimate_license_status', 'image_hover_ultimate_license_key' ];

    const TRANSIENTS = [ 'oxi_image_user_permission_role', 'oxi_image_hover_activation_redirect' ];

    /**
     * Count what delete_now() would remove, for the confirmation dialog.
     *
     * @return array{shortcodes:int,items:int}
     */
    public static function counts() {
        global $wpdb;
        $style = $wpdb->prefix . 'image_hover_ultimate_style';
        $list  = $wpdb->prefix . 'image_hover_ultimate_list';
        return [
            'shortcodes' => (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . esc_sql( $style ) ), // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            'items'      => (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . esc_sql( $list ) ), // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        ];
    }

    /**
     * Delete every shortcode, image item and setting, keep the plugin usable.
     *
     * Rows are deleted rather than the tables truncated, so new shortcodes
     * never reuse an old ID and suddenly appear inside an old post. Install
     * state, notice dismissals, widget instances and the licence are kept.
     */
    public static function delete_now() {
        global $wpdb;
        $wpdb->query( 'DELETE FROM ' . esc_sql( $wpdb->prefix . 'image_hover_ultimate_list' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $wpdb->query( 'DELETE FROM ' . esc_sql( $wpdb->prefix . 'image_hover_ultimate_style' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        self::delete_import_rows();

        foreach ( self::SETTINGS as $option ) {
            delete_option( $option );
        }
        self::delete_shared_options();
        foreach ( self::TRANSIENTS as $transient ) {
            delete_transient( $transient );
        }
    }

    /**
     * Freemius after_uninstall callback.
     */
    public static function uninstall() {
        if ( is_multisite() ) {
            foreach ( get_sites( [ 'fields' => 'ids', 'number' => 0 ] ) as $site_id ) {
                switch_to_blog( $site_id );
                self::uninstall_site();
                restore_current_blog();
            }
            return;
        }
        self::uninstall_site();
    }

    /**
     * Remove everything on the current site, if the site opted in.
     */
    public static function uninstall_site() {
        if ( 'yes' !== get_option( self::UNINSTALL_OPTION ) ) {
            return;
        }
        global $wpdb;
        $wpdb->query( 'DROP TABLE IF EXISTS ' . esc_sql( $wpdb->prefix . 'image_hover_ultimate_list' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
        $wpdb->query( 'DROP TABLE IF EXISTS ' . esc_sql( $wpdb->prefix . 'image_hover_ultimate_style' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
        self::delete_import_rows();

        foreach ( array_merge( self::SETTINGS, self::OPTIONS, self::LICENSE_OPTIONS ) as $option ) {
            delete_option( $option );
        }
        self::delete_shared_options();
        foreach ( self::TRANSIENTS as $transient ) {
            delete_transient( $transient );
        }
        delete_option( self::UNINSTALL_OPTION );
    }

    /**
     * Delete this plugin's rows from the shared oxi_div_import table.
     */
    public static function delete_import_rows() {
        global $wpdb;
        $table = $wpdb->prefix . 'oxi_div_import';
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) !== $table ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            return;
        }
        $types        = array_map(
            function ( $module ) {
                return $module . '-ultimate';
            },
            self::MODULES
        );
        $placeholders = implode( ', ', array_fill( 0, count( $types ), '%s' ) );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
        $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . esc_sql( $table ) . " WHERE type IN ( $placeholders )", $types ) );
    }

    /**
     * Delete the options shared with Flipbox, unless another Oxilab plugin is installed.
     */
    public static function delete_shared_options() {
        if ( self::other_oxilab_plugin_installed() ) {
            return;
        }
        foreach ( self::SHARED_OPTIONS as $option ) {
            delete_option( $option );
        }
    }

    /**
     * Whether any other installed plugin is made by Oxilab.
     *
     * @return bool
     */
    public static function other_oxilab_plugin_installed() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $self = defined( 'OXI_IMAGE_HOVER_BASENAME' ) ? OXI_IMAGE_HOVER_BASENAME : 'image-hover-effects-ultimate/index.php';
        foreach ( get_plugins() as $file => $data ) {
            if ( $file === $self ) {
                continue;
            }
            if ( false !== stripos( $data['Author'] . ' ' . $data['AuthorURI'], 'oxilab' ) ) {
                return true;
            }
        }
        return false;
    }
}
