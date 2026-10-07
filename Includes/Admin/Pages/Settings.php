<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Includes\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Description of Settings
 *
 * @author
 */
class Settings {

    use \OXI_IMAGE_HOVER_PLUGINS\Helper\CSS_JS_Loader;

    public $roles;
    public $saved_role;
    public $oxi_fixed_header;
    public $fontawesome;
    public $getfontawesome = [];

    /**
     * Constructor of Oxilab tabs Home Page
     *
     * @since 9.3.0
     */
    public function __construct() {
        $this->admin();
        $this->css_loader();
        $this->Render();
    }

    public function admin() {
        global $wp_roles;
        $this->roles      = $wp_roles->get_names();
        $this->saved_role = get_option( 'oxi_image_user_permission' );
    }

    public function css_loader() {
        $this->admin_js();
        wp_enqueue_script(
            'oxi-image-hover-settings',
            OXI_IMAGE_HOVER_URL . 'assets/backend/js/settings.js',
            [ 'jquery' ],
            filemtime( OXI_IMAGE_HOVER_PATH . 'assets/backend/js/settings.js' ),
            true
        );
    }

    /**
     * Render an on/off switch row.
     *
     * Each option is "on" unless it holds its off value, which is how the
     * frontend reads it, so an option that was never saved shows as on.
     *
     * @since 9.12.0
     *
     * @param string $name        Option name, also the AJAX function name.
     * @param string $label       Row label.
     * @param string $description Row help text.
     * @param string $on          Value stored when switched on.
     * @param string $off         Value stored when switched off.
     * @param bool   $default_on  Whether an option that was never saved shows as on.
     */
    public function switch_row( $name, $label, $description, $on, $off, $default_on = true ) {
        $checked = $default_on ? get_option( $name ) !== $off : get_option( $name ) === $on;
        ?>
        <div class="oxi-iheu-set-row">
            <div class="oxi-iheu-set-row-text">
                <label class="oxi-iheu-set-label" for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
                <p class="oxi-iheu-set-desc" id="<?php echo esc_attr( $name ); ?>-desc"><?php echo esc_html( $description ); ?></p>
            </div>
            <div class="oxi-iheu-set-row-control">
                <span class="oxi-iheu-set-status" data-status-for="<?php echo esc_attr( $name ); ?>" aria-live="polite"></span>
                <span class="oxi-iheu-set-switch">
                    <input type="checkbox" role="switch" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" data-on="<?php echo esc_attr( $on ); ?>" data-off="<?php echo esc_attr( $off ); ?>" aria-describedby="<?php echo esc_attr( $name ); ?>-desc" <?php checked( $checked ); ?>>
                    <span class="oxi-iheu-set-switch-track" aria-hidden="true"><span class="oxi-iheu-set-switch-thumb"></span></span>
                </span>
            </div>
        </div>
        <?php
    }

    /**
     * Render a card header.
     *
     * @since 9.12.0
     *
     * @param string $icon        Dashicons class suffix.
     * @param string $title       Card title.
     * @param string $description Card subtitle.
     */
    public function card_head( $icon, $title, $description ) {
        ?>
        <div class="oxi-iheu-set-card-head">
            <span class="oxi-iheu-set-card-icon dashicons dashicons-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></span>
            <div>
                <h2 class="oxi-iheu-set-card-title"><?php echo esc_html( $title ); ?></h2>
                <p class="oxi-iheu-set-card-sub"><?php echo esc_html( $description ); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Read the licence state from Freemius and the legacy licence option.
     *
     * Wrapped defensively: a Freemius error must never break the settings page.
     *
     * @since 9.12.0
     *
     * @return array
     */
    public function license_state() {
        $state = [
            'status'      => 'free',
            'plan'        => '',
            'expires'     => '',
            'key'         => '',
            'can_manage'  => false,
            'affix'       => '',
            'account_url' => '',
        ];
        try {
            if ( function_exists( 'oxilab_iheu_v' ) ) {
                $fs      = oxilab_iheu_v();
                $license = $fs->_get_license();

                $state['can_manage'] = $fs->is_user_admin();
                $state['affix']      = $fs->get_unique_affix();
                $state['plan']       = $fs->get_plan_title();
                if ( $fs->is_registered() ) {
                    $state['account_url'] = $fs->get_account_url();
                }

                if ( is_object( $license ) ) {
                    $state['key']     = $license->get_html_escaped_masked_secret_key();
                    $state['expires'] = $license->is_lifetime() ? '' : date_i18n( get_option( 'date_format' ), strtotime( $license->expiration ) );
                }

                if ( $fs->is_trial() ) {
                    $state['status'] = 'trial';
                } elseif ( $fs->can_use_premium_code() ) {
                    $state['status'] = 'active';
                } elseif ( is_object( $license ) && $license->is_expired() ) {
                    $state['status'] = 'expired';
                }
            }
        } catch ( \Throwable $e ) {
            $state['can_manage'] = false;
        }

        if ( 'free' === $state['status'] && 'valid' === get_option( 'image_hover_ultimate_license_status' ) ) {
            $state['status'] = 'legacy';
        }
        return $state;
    }

    /**
     * Render the licence card.
     *
     * The Activate/Change buttons open Freemius' own licence dialog, the same
     * one behind "Activate License" on the Plugins screen. Freemius only
     * prints that dialog on the Plugins screen, so it is added to this page's
     * footer here; its AJAX handler is registered on every admin request.
     *
     * @since 9.12.0
     */
    public function license_card() {
        $state   = $this->license_state();
        $status  = $state['status'];
        $pricing = 'https://oxilab.dev/image-hover-effects/pricing/';

        $show_dialog = $state['can_manage'] && '' !== $state['affix'] && 'legacy' !== $status;
        if ( $show_dialog ) {
            add_action( 'admin_footer', [ oxilab_iheu_v(), '_add_license_activation_dialog_box' ] );
        }
        $trigger = 'activate-license-trigger ' . $state['affix'];

        $pills = [
            'free'    => __( 'Free plan', 'image-hover-effects-ultimate' ),
            'trial'   => __( 'Trial', 'image-hover-effects-ultimate' ),
            'active'  => __( 'Active', 'image-hover-effects-ultimate' ),
            'expired' => __( 'Expired', 'image-hover-effects-ultimate' ),
            'legacy'  => __( 'Active', 'image-hover-effects-ultimate' ),
        ];
        ?>
        <section class="oxi-iheu-set-card oxi-iheu-set-license is-<?php echo esc_attr( $status ); ?>" id="license">
            <div class="oxi-iheu-set-card-head">
                <span class="oxi-iheu-set-card-icon dashicons dashicons-admin-network" aria-hidden="true"></span>
                <div>
                    <h2 class="oxi-iheu-set-card-title"><?php esc_html_e( 'License', 'image-hover-effects-ultimate' ); ?></h2>
                    <p class="oxi-iheu-set-card-sub"><?php esc_html_e( 'Your Image Hover Effects Pro license for this site.', 'image-hover-effects-ultimate' ); ?></p>
                </div>
                <span class="oxi-iheu-set-pill is-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $pills[ $status ] ); ?></span>
            </div>

            <div class="oxi-iheu-set-license-body">
                <?php if ( in_array( $status, [ 'active', 'trial', 'expired' ], true ) && ( '' !== $state['plan'] || '' !== $state['key'] ) ) : ?>
                    <dl class="oxi-iheu-set-license-facts">
                        <?php if ( '' !== $state['plan'] && 'expired' !== $status ) : ?>
                            <div>
                                <dt><?php esc_html_e( 'Plan', 'image-hover-effects-ultimate' ); ?></dt>
                                <dd><?php echo esc_html( $state['plan'] ); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if ( '' !== $state['key'] ) : ?>
                            <div>
                                <dt><?php echo esc_html( 'expired' === $status ? __( 'Expired on', 'image-hover-effects-ultimate' ) : __( 'Expires', 'image-hover-effects-ultimate' ) ); ?></dt>
                                <dd><?php echo esc_html( '' !== $state['expires'] ? $state['expires'] : __( 'Never (lifetime)', 'image-hover-effects-ultimate' ) ); ?></dd>
                            </div>
                            <div>
                                <dt><?php esc_html_e( 'License key', 'image-hover-effects-ultimate' ); ?></dt>
                                <dd class="oxi-iheu-set-license-key"><?php echo wp_kses( $state['key'], [] ); ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                <?php endif; ?>

                <p class="oxi-iheu-set-license-text">
                    <?php
                    if ( 'active' === $status ) :
                        esc_html_e( 'Pro is active on this site. Thank you for supporting Image Hover Effects.', 'image-hover-effects-ultimate' );
                    elseif ( 'trial' === $status ) :
                        esc_html_e( 'Your Pro trial is running. Activate a license key to keep Pro when the trial ends.', 'image-hover-effects-ultimate' );
                    elseif ( 'expired' === $status ) :
                        esc_html_e( 'Your license has expired. Renew it to get Pro back on this site.', 'image-hover-effects-ultimate' );
                    elseif ( 'legacy' === $status ) :
                        esc_html_e( 'Pro is active on this site with a license key from an earlier version.', 'image-hover-effects-ultimate' );
                    elseif ( ! $state['can_manage'] ) :
                        esc_html_e( 'Pro is not active on this site yet.', 'image-hover-effects-ultimate' );
                    else :
                        esc_html_e( 'Bought Pro? Paste the license key from your purchase email to activate it here.', 'image-hover-effects-ultimate' );
                    endif;
                    ?>
                </p>

                <?php ob_start(); ?>
                    <?php if ( $show_dialog ) : ?>
                        <?php if ( 'active' === $status ) : ?>
                            <a href="#" class="oxi-iheu-set-btn is-secondary <?php echo esc_attr( $trigger ); ?>"><?php esc_html_e( 'Change license', 'image-hover-effects-ultimate' ); ?></a>
                        <?php else : ?>
                            <a href="#" class="oxi-iheu-set-btn is-primary <?php echo esc_attr( $trigger ); ?>">
                                <span class="dashicons dashicons-admin-network" aria-hidden="true"></span><?php esc_html_e( 'Activate license', 'image-hover-effects-ultimate' ); ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ( 'expired' === $status ) : ?>
                        <a class="oxi-iheu-set-btn is-secondary" target="_blank" rel="noopener" href="<?php echo esc_url( $pricing ); ?>"><?php esc_html_e( 'Renew license', 'image-hover-effects-ultimate' ); ?></a>
                    <?php elseif ( $state['can_manage'] && ( 'free' === $status || 'trial' === $status ) ) : ?>
                        <a class="oxi-iheu-set-btn is-secondary" target="_blank" rel="noopener" href="<?php echo esc_url( $pricing ); ?>"><?php esc_html_e( 'Get Pro', 'image-hover-effects-ultimate' ); ?></a>
                    <?php endif; ?>

                    <?php if ( $state['can_manage'] && '' !== $state['account_url'] && 'free' !== $status ) : ?>
                        <a class="oxi-iheu-set-link" href="<?php echo esc_url( $state['account_url'] ); ?>"><?php esc_html_e( 'Manage account', 'image-hover-effects-ultimate' ); ?></a>
                    <?php endif; ?>

                    <?php if ( ! $state['can_manage'] && 'legacy' !== $status && 'active' !== $status ) : ?>
                        <span class="oxi-iheu-set-license-note"><?php esc_html_e( 'Ask a site administrator to activate the license.', 'image-hover-effects-ultimate' ); ?></span>
                    <?php endif; ?>
                <?php
                $actions = trim( ob_get_clean() );
                if ( '' !== $actions ) :
                    ?>
                    <div class="oxi-iheu-set-actions"><?php echo $actions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built above, every value escaped there. ?></div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }

    /**
     * Render the Danger zone card and its confirmation dialog.
     *
     * Administrators only: these actions remove data for the whole site.
     *
     * @since 9.12.0
     */
    public function danger_zone() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $counts = \OXI_IMAGE_HOVER_PLUGINS\Classes\Data_Cleaner::counts();
        /* translators: %s: number of shortcodes */
        $shortcodes = sprintf( _n( '%s shortcode', '%s shortcodes', $counts['shortcodes'], 'image-hover-effects-ultimate' ), number_format_i18n( $counts['shortcodes'] ) );
        /* translators: %s: number of image items */
        $items = sprintf( _n( '%s image item', '%s image items', $counts['items'], 'image-hover-effects-ultimate' ), number_format_i18n( $counts['items'] ) );
        ?>
        <section class="oxi-iheu-set-card oxi-iheu-set-danger" aria-labelledby="oxi-iheu-danger-title">
            <div class="oxi-iheu-set-card-head">
                <span class="oxi-iheu-set-card-icon dashicons dashicons-warning" aria-hidden="true"></span>
                <div>
                    <h2 class="oxi-iheu-set-card-title" id="oxi-iheu-danger-title"><?php esc_html_e( 'Danger zone', 'image-hover-effects-ultimate' ); ?></h2>
                    <p class="oxi-iheu-set-card-sub"><?php esc_html_e( 'These actions permanently remove your Image Hover data.', 'image-hover-effects-ultimate' ); ?></p>
                </div>
            </div>
            <?php
            $this->switch_row(
                \OXI_IMAGE_HOVER_PLUGINS\Classes\Data_Cleaner::UNINSTALL_OPTION,
                __( 'Delete data when the plugin is deleted', 'image-hover-effects-ultimate' ),
                __( 'When you delete Image Hover Effects from the Plugins screen, also remove its shortcodes, image items and settings. Deactivating never removes data, so you can safely deactivate while troubleshooting.', 'image-hover-effects-ultimate' ),
                'yes',
                'no',
                false
            );
            ?>
            <div class="oxi-iheu-set-row">
                <div class="oxi-iheu-set-row-text">
                    <span class="oxi-iheu-set-label"><?php esc_html_e( 'Delete all data now', 'image-hover-effects-ultimate' ); ?></span>
                    <p class="oxi-iheu-set-desc">
                        <?php
                        echo esc_html(
                            sprintf(
                                /* translators: 1: number of shortcodes, 2: number of image items */
                                __( 'Remove %1$s, %2$s and every setting on this page. Your license stays active.', 'image-hover-effects-ultimate' ),
                                $shortcodes,
                                $items
                            )
                        );
                        ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-image-hover-shortcode' ) ); ?>"><?php esc_html_e( 'Export what you want to keep first.', 'image-hover-effects-ultimate' ); ?></a>
                    </p>
                </div>
                <div class="oxi-iheu-set-row-control">
                    <button type="button" class="oxi-iheu-set-btn is-danger" data-oxi-iheu-open="oxi-iheu-delete-dialog">
                        <span class="dashicons dashicons-trash" aria-hidden="true"></span><?php esc_html_e( 'Delete all data', 'image-hover-effects-ultimate' ); ?>
                    </button>
                </div>
            </div>
        </section>

        <div class="oxi-iheu-set-dialog" id="oxi-iheu-delete-dialog" hidden>
            <div class="oxi-iheu-set-dialog-backdrop" data-oxi-iheu-close></div>
            <div class="oxi-iheu-set-dialog-box" role="alertdialog" aria-modal="true" aria-labelledby="oxi-iheu-delete-title" aria-describedby="oxi-iheu-delete-desc">
                <span class="oxi-iheu-set-dialog-icon dashicons dashicons-warning" aria-hidden="true"></span>
                <h2 class="oxi-iheu-set-dialog-title" id="oxi-iheu-delete-title"><?php esc_html_e( 'Delete all Image Hover data?', 'image-hover-effects-ultimate' ); ?></h2>
                <div id="oxi-iheu-delete-desc">
                    <p class="oxi-iheu-set-dialog-text"><?php esc_html_e( 'This permanently deletes:', 'image-hover-effects-ultimate' ); ?></p>
                    <ul class="oxi-iheu-set-dialog-list">
                        <li><?php echo esc_html( $shortcodes ); ?></li>
                        <li><?php echo esc_html( $items ); ?></li>
                        <li><?php esc_html_e( 'Every setting on this page', 'image-hover-effects-ultimate' ); ?></li>
                    </ul>
                    <p class="oxi-iheu-set-dialog-text"><?php esc_html_e( 'Pages that use an Image Hover shortcode will show nothing in its place. This cannot be undone.', 'image-hover-effects-ultimate' ); ?></p>
                </div>
                <label class="oxi-iheu-set-dialog-label" for="oxi-iheu-delete-confirm">
                    <?php
                    printf(
                        /* translators: %s: the word the user must type */
                        esc_html__( 'Type %s to confirm', 'image-hover-effects-ultimate' ),
                        '<code>DELETE</code>'
                    );
                    ?>
                </label>
                <input type="text" class="oxi-iheu-set-input" id="oxi-iheu-delete-confirm" autocomplete="off" spellcheck="false" autocapitalize="characters">
                <p class="oxi-iheu-set-dialog-status" role="status" aria-live="polite"
                    data-deleting="<?php esc_attr_e( 'Deleting all data', 'image-hover-effects-ultimate' ); ?>"
                    data-done="<?php esc_attr_e( 'All data deleted. Reloading the page.', 'image-hover-effects-ultimate' ); ?>"
                    data-error="<?php esc_attr_e( 'Nothing was deleted. Reload the page and try again.', 'image-hover-effects-ultimate' ); ?>"></p>
                <div class="oxi-iheu-set-dialog-actions">
                    <button type="button" class="oxi-iheu-set-btn is-secondary" data-oxi-iheu-close><?php esc_html_e( 'Cancel', 'image-hover-effects-ultimate' ); ?></button>
                    <button type="button" class="oxi-iheu-set-btn is-danger-solid" id="oxi-iheu-delete-submit" disabled><?php esc_html_e( 'Delete everything', 'image-hover-effects-ultimate' ); ?></button>
                </div>
            </div>
        </div>
        <?php
    }

    public function Render() {
        $is_pro = apply_filters( 'oxi-image-hover-plugin-version', false ) != false;
        ?>
        <div class="wrap">
            <?php apply_filters( 'oxi-image-hover-plugin/admin_menu', true ); ?>
            <hr class="wp-header-end">

            <div class="oxi-iheu-settings"
                data-saving="<?php esc_attr_e( 'Saving', 'image-hover-effects-ultimate' ); ?>"
                data-saved="<?php esc_attr_e( 'Saved', 'image-hover-effects-ultimate' ); ?>"
                data-error="<?php esc_attr_e( 'Not saved, try again', 'image-hover-effects-ultimate' ); ?>">

                <header class="oxi-iheu-set-hero">
                    <img class="oxi-iheu-set-hero-logo" src="<?php echo esc_url( OXI_IMAGE_HOVER_URL . 'image/logo.png' ); ?>" alt="" width="52" height="52">
                    <div class="oxi-iheu-set-hero-text">
                        <h1 class="oxi-iheu-set-title"><?php esc_html_e( 'Settings', 'image-hover-effects-ultimate' ); ?></h1>
                        <p class="oxi-iheu-set-subtitle"><?php esc_html_e( 'Control who manages Image Hover Effects and which assets load with your shortcodes.', 'image-hover-effects-ultimate' ); ?></p>
                    </div>
                    <span class="oxi-iheu-set-autosave">
                        <span class="oxi-iheu-set-autosave-dot" aria-hidden="true"></span>
                        <?php esc_html_e( 'Changes save automatically', 'image-hover-effects-ultimate' ); ?>
                    </span>
                </header>

                <div class="oxi-iheu-set-layout">
                    <div class="oxi-iheu-set-main">

                        <?php $this->license_card(); ?>

                        <section class="oxi-iheu-set-card">
                            <?php $this->card_head( 'groups', __( 'Access', 'image-hover-effects-ultimate' ), __( 'Decide who can create and edit hover effects.', 'image-hover-effects-ultimate' ) ); ?>
                            <div class="oxi-iheu-set-row">
                                <div class="oxi-iheu-set-row-text">
                                    <label class="oxi-iheu-set-label" for="oxi_image_user_permission"><?php esc_html_e( 'Who can edit', 'image-hover-effects-ultimate' ); ?></label>
                                    <p class="oxi-iheu-set-desc">
                                        <?php esc_html_e( 'Select the role who can manage this plugin.', 'image-hover-effects-ultimate' ); ?>
                                        <a target="_blank" rel="noopener" href="https://wordpress.org/documentation/article/roles-and-capabilities/"><?php esc_html_e( 'About roles', 'image-hover-effects-ultimate' ); ?></a>
                                    </p>
                                </div>
                                <div class="oxi-iheu-set-row-control">
                                    <span class="oxi-iheu-set-status" data-status-for="oxi_image_user_permission" aria-live="polite"></span>
                                    <select class="oxi-iheu-set-select" name="oxi_image_user_permission" id="oxi_image_user_permission">
                                        <?php foreach ( $this->roles as $key => $role ) : ?>
                                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $this->saved_role, $key ); ?>><?php echo esc_html( translate_user_role( $role ) ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </section>

                        <section class="oxi-iheu-set-card">
                            <?php $this->card_head( 'smartphone', __( 'Mobile and touch', 'image-hover-effects-ultimate' ), __( 'How hover effects respond on phones and tablets.', 'image-hover-effects-ultimate' ) ); ?>
                            <?php
                            $this->switch_row(
                                'image_hover_ultimate_mobile_device_key',
                                __( 'Show the effect on first tap', 'image-hover-effects-ultimate' ),
                                __( 'On: the first tap shows the hover effect and a second tap opens the link. Off: a tap opens the link right away.', 'image-hover-effects-ultimate' ),
                                '',
                                'normal'
                            );
                            ?>
                        </section>

                        <section class="oxi-iheu-set-card">
                            <?php $this->card_head( 'performance', __( 'Assets and performance', 'image-hover-effects-ultimate' ), __( 'Turn off anything your theme already loads to keep pages lighter.', 'image-hover-effects-ultimate' ) ); ?>
                            <?php
                            $this->switch_row(
                                'oxi_addons_font_awesome',
                                __( 'Font Awesome', 'image-hover-effects-ultimate' ),
                                __( 'Load Font Awesome CSS when a shortcode shows icons. If your theme already loads it, turn this off for faster loading.', 'image-hover-effects-ultimate' ),
                                'yes',
                                'no'
                            );
                            $this->switch_row(
                                'oxi_addons_way_points',
                                __( 'Waypoints', 'image-hover-effects-ultimate' ),
                                __( 'Load the Waypoints script used by entrance animations. If your theme already loads it, turn this off for faster loading.', 'image-hover-effects-ultimate' ),
                                '',
                                'no'
                            );
                            $this->switch_row(
                                'oxi_addons_google_font',
                                __( 'Google Fonts', 'image-hover-effects-ultimate' ),
                                __( 'Load the fonts used in your effects from Google. If you already load those fonts locally, turn this off for faster loading.', 'image-hover-effects-ultimate' ),
                                '',
                                'no'
                            );
                            ?>
                        </section>

                        <section class="oxi-iheu-set-card">
                            <?php $this->card_head( 'admin-tools', __( 'Advanced', 'image-hover-effects-ultimate' ), __( 'Fine tune output and the admin area.', 'image-hover-effects-ultimate' ) ); ?>
                            <div class="oxi-iheu-set-row">
                                <div class="oxi-iheu-set-row-text">
                                    <label class="oxi-iheu-set-label" for="oxi_addons_custom_parent_class"><?php esc_html_e( 'Custom parent class', 'image-hover-effects-ultimate' ); ?></label>
                                    <p class="oxi-iheu-set-desc" id="oxi_addons_custom_parent_class-desc"><?php esc_html_e( 'Add a CSS class to every Image Hover wrapper to avoid conflicts with your theme or other plugins.', 'image-hover-effects-ultimate' ); ?></p>
                                </div>
                                <div class="oxi-iheu-set-row-control">
                                    <span class="oxi-iheu-set-status" data-status-for="oxi_addons_custom_parent_class" aria-live="polite"></span>
                                    <input type="text" class="oxi-iheu-set-input" id="oxi_addons_custom_parent_class" name="oxi_addons_custom_parent_class" value="<?php echo esc_attr( get_option( 'oxi_addons_custom_parent_class' ) ); ?>" placeholder="my-hover-wrap" spellcheck="false" autocomplete="off" aria-describedby="oxi_addons_custom_parent_class-desc">
                                </div>
                            </div>
                            <?php
                            $this->switch_row(
                                'oxi_image_support_massage',
                                __( 'Support message', 'image-hover-effects-ultimate' ),
                                __( 'Display the support message in the Image Hover admin area.', 'image-hover-effects-ultimate' ),
                                '',
                                'no'
                            );
                            ?>
                        </section>

                        <?php $this->danger_zone(); ?>
                    </div>

                    <aside class="oxi-iheu-set-aside">
                        <div class="oxi-iheu-set-card oxi-iheu-set-help">
                            <h2 class="oxi-iheu-set-card-title"><?php esc_html_e( 'Need a hand?', 'image-hover-effects-ultimate' ); ?></h2>
                            <p class="oxi-iheu-set-card-sub"><?php esc_html_e( 'Guides, live examples and answers from our team.', 'image-hover-effects-ultimate' ); ?></p>
                            <ul class="oxi-iheu-set-links">
                                <li>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=image-hover-ultimate-getting-started' ) ); ?>">
                                        <span class="dashicons dashicons-flag" aria-hidden="true"></span><?php esc_html_e( 'Getting started', 'image-hover-effects-ultimate' ); ?>
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" rel="noopener" href="https://oxilab.dev/docs/image-hover-effects/">
                                        <span class="dashicons dashicons-book-alt" aria-hidden="true"></span><?php esc_html_e( 'Documentation', 'image-hover-effects-ultimate' ); ?><span class="oxi-iheu-set-ext dashicons dashicons-external" aria-hidden="true"></span>
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" rel="noopener" href="https://demos.oxilab.dev/imagehover/demos/">
                                        <span class="dashicons dashicons-visibility" aria-hidden="true"></span><?php esc_html_e( 'Live demos', 'image-hover-effects-ultimate' ); ?><span class="oxi-iheu-set-ext dashicons dashicons-external" aria-hidden="true"></span>
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" rel="noopener" href="https://wordpress.org/support/plugin/image-hover-effects-ultimate/">
                                        <span class="dashicons dashicons-sos" aria-hidden="true"></span><?php esc_html_e( 'Support forum', 'image-hover-effects-ultimate' ); ?><span class="oxi-iheu-set-ext dashicons dashicons-external" aria-hidden="true"></span>
                                    </a>
                                </li>
                            </ul>
                            <div class="oxi-iheu-set-meta">
                                <span><?php echo esc_html( sprintf( /* translators: %s: plugin version */ __( 'Version %s', 'image-hover-effects-ultimate' ), OXI_IMAGE_HOVER_PLUGIN_VERSION ) ); ?></span>
                                <?php if ( $is_pro ) : ?>
                                    <span class="oxi-iheu-set-plan is-pro"><?php esc_html_e( 'Premium version', 'image-hover-effects-ultimate' ); ?></span>
                                <?php else : ?>
                                    <span class="oxi-iheu-set-plan"><?php esc_html_e( 'Free', 'image-hover-effects-ultimate' ); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
        <?php
    }
}
