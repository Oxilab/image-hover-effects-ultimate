<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Includes\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Brands the Freemius Account page.
 *
 * Freemius renders this page from its own template, so nothing in the SDK
 * is edited. Its "templates/account.php" filter (meant for wrapping the
 * template) adds the plugin header and a scoping wrapper, and
 * assets/backend/css/account.css restyles the page inside that wrapper.
 *
 * @since 9.11.9
 */
class Account {

    public function __construct() {
        if ( ! function_exists( 'oxilab_iheu_v' ) ) {
            return;
        }
        $fs = oxilab_iheu_v();
        // The page only has one tab, so the tab bar adds nothing.
        $fs->add_filter( 'hide_account_tabs', '__return_true' );
        $fs->add_filter( 'templates/account.php', [ $this, 'wrap' ] );
        add_filter( 'admin_body_class', [ $this, 'body_class' ] );
    }

    /**
     * Mark the pages that load admin-menu.css (Account, Getting Started) so
     * it can make the plugin header full width there.
     *
     * @param string $classes Admin body classes.
     * @return string
     */
    public function body_class( $classes ) {
        $pages = [ 'oxi-image-hover-ultimate-account', 'image-hover-ultimate-getting-started' ];
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $pages, true ) ) {
            $classes .= ' oxi-iheu-menu-page';
        }
        return $classes;
    }

    /**
     * Wrap the Freemius account template.
     *
     * @param string $html Rendered Freemius template.
     * @return string
     */
    public function wrap( $html ) {
        ob_start();
        // The plugin's header menu, same as on the other plugin pages.
        apply_filters( 'oxi-image-hover-plugin/admin_menu', true );

        // account.css shows this label after the version number, as a CSS
        // string, so it needs CSS string escaping before HTML escaping.
        $is_premium = apply_filters( 'oxi-image-hover-plugin-version', false ) != false;
        $label      = '"' . addcslashes( __( 'Premium version', 'image-hover-effects-ultimate' ), '"\\' ) . '"';
        ?>
        <div class="oxi-iheu-settings oxi-iheu-account<?php echo $is_premium ? ' is-premium' : ''; ?>" style="<?php echo esc_attr( '--iheu-premium-label: ' . $label ); ?>">
            <header class="oxi-iheu-set-hero">
                <img class="oxi-iheu-set-hero-logo" src="<?php echo esc_url( OXI_IMAGE_HOVER_URL . 'image/logo.png' ); ?>" alt="" width="52" height="52">
                <div class="oxi-iheu-set-hero-text">
                    <h1 class="oxi-iheu-set-title"><?php esc_html_e( 'Account', 'image-hover-effects-ultimate' ); ?></h1>
                    <p class="oxi-iheu-set-subtitle"><?php esc_html_e( 'Your Image Hover Effects license, billing details and invoices.', 'image-hover-effects-ultimate' ); ?></p>
                </div>
                <a class="oxi-iheu-set-btn is-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-image-hover-ultimate-settings' ) ); ?>">
                    <span class="dashicons dashicons-admin-generic" aria-hidden="true"></span><?php esc_html_e( 'Settings', 'image-hover-effects-ultimate' ); ?>
                </a>
            </header>
            <hr class="wp-header-end">
        <?php
        return ob_get_clean() . $html . '</div>';
    }
}
