<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Includes\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Shortcode {

    /**
     * Database Parent Table
     *
     * @since 9.3.0
     */
    public $parent_table;

    /**
     * Database Import Table
     *
     * @since 9.3.0
     */
    public $child_table;

    /**
     * Database Import Table
     *
     * @since 9.3.0
     */
    public $import_table;

    /**
     * Define $wpdb
     *
     * @since 9.3.0
     */
    public $wpdb;

    use \OXI_IMAGE_HOVER_PLUGINS\Helper\Public_Helper;
    use \OXI_IMAGE_HOVER_PLUGINS\Helper\CSS_JS_Loader;


    /**
     * Constructor of Oxilab tabs Home Page
     *
     * @since 9.3.0
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->parent_table = $this->wpdb->prefix . 'image_hover_ultimate_style';
        $this->child_table = $this->wpdb->prefix . 'image_hover_ultimate_list';
        $this->import_table = $this->wpdb->prefix . 'oxi_div_import';

        $this->CSSJS_load();
        $this->Render();
    }

    public function Render() {
        $rows = $this->database_data();
        ?>
        <hr class="wp-header-end">
        <div class="oxi-iheu-settings oxi-iheu-shortcodes"
            data-copied="<?php esc_attr_e( 'Copied', 'image-hover-effects-ultimate' ); ?>"
            data-copy-failed="<?php esc_attr_e( 'Press Ctrl+C to copy', 'image-hover-effects-ultimate' ); ?>"
            data-search="<?php esc_attr_e( 'Search shortcodes', 'image-hover-effects-ultimate' ); ?>"
            data-per-page="<?php esc_attr_e( '_MENU_ per page', 'image-hover-effects-ultimate' ); ?>"
            data-info="<?php esc_attr_e( 'Showing _START_ to _END_ of _TOTAL_', 'image-hover-effects-ultimate' ); ?>"
            data-info-empty="<?php esc_attr_e( 'No shortcodes to show', 'image-hover-effects-ultimate' ); ?>"
            data-info-filtered="<?php esc_attr_e( '(filtered from _MAX_)', 'image-hover-effects-ultimate' ); ?>"
            data-zero="<?php esc_attr_e( 'No shortcodes match your search.', 'image-hover-effects-ultimate' ); ?>"
            data-prev="<?php esc_attr_e( 'Previous', 'image-hover-effects-ultimate' ); ?>"
            data-next="<?php esc_attr_e( 'Next', 'image-hover-effects-ultimate' ); ?>"
            data-all="<?php esc_attr_e( 'All', 'image-hover-effects-ultimate' ); ?>">
            <?php
            $this->Admin_header( count( $rows ) );
            if ( empty( $rows ) ) {
                $this->empty_state();
            } else {
                $this->created_shortcode( $rows );
            }
            $this->create_new();
            ?>
        </div>
        <?php
    }

    public function Admin_header( $total = 0 ) {
        ?>
        <header class="oxi-iheu-set-hero">
            <img class="oxi-iheu-set-hero-logo" src="<?php echo esc_url( OXI_IMAGE_HOVER_URL . 'image/logo.png' ); ?>" alt="" width="52" height="52">
            <div class="oxi-iheu-set-hero-text">
                <h1 class="oxi-iheu-set-title">
                    <?php esc_html_e( 'Shortcodes', 'image-hover-effects-ultimate' ); ?>
                    <span class="oxi-iheu-sc-count"><?php echo esc_html( number_format_i18n( $total ) ); ?></span>
                </h1>
                <p class="oxi-iheu-set-subtitle"><?php esc_html_e( 'Copy a shortcode into any page or post, or edit, clone, export and delete it.', 'image-hover-effects-ultimate' ); ?></p>
            </div>
            <div class="oxi-iheu-sc-hero-actions">
                <button type="button" class="oxi-iheu-set-btn is-secondary" data-oxi-iheu-open="oxi-iheu-import-dialog">
                    <span class="dashicons dashicons-upload" aria-hidden="true"></span><?php esc_html_e( 'Import', 'image-hover-effects-ultimate' ); ?>
                </button>
                <a class="oxi-iheu-set-btn is-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-image-hover-ultimate' ) ); ?>">
                    <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'Create new', 'image-hover-effects-ultimate' ); ?>
                </a>
            </div>
        </header>
        <?php
        apply_filters( 'oxi-image-hover-support-and-comments', true );
    }

    /**
     * Shown when there are no shortcodes yet.
     *
     * @since 9.12.0
     */
    public function empty_state() {
        ?>
        <section class="oxi-iheu-set-card oxi-iheu-sc-empty">
            <span class="oxi-iheu-sc-empty-icon dashicons dashicons-format-gallery" aria-hidden="true"></span>
            <h2 class="oxi-iheu-set-card-title"><?php esc_html_e( 'No shortcodes yet', 'image-hover-effects-ultimate' ); ?></h2>
            <p class="oxi-iheu-set-card-sub"><?php esc_html_e( 'Pick an effect, design it, and its shortcode will appear here ready to copy.', 'image-hover-effects-ultimate' ); ?></p>
            <div class="oxi-iheu-set-actions">
                <a class="oxi-iheu-set-btn is-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-image-hover-ultimate' ) ); ?>">
                    <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'Create your first effect', 'image-hover-effects-ultimate' ); ?>
                </a>
                <button type="button" class="oxi-iheu-set-btn is-secondary" data-oxi-iheu-open="oxi-iheu-import-dialog">
                    <span class="dashicons dashicons-upload" aria-hidden="true"></span><?php esc_html_e( 'Import a JSON file', 'image-hover-effects-ultimate' ); ?>
                </button>
            </div>
        </section>
        <?php
    }

    public function create_new() {
        ?>
        <div class="oxi-iheu-set-dialog" id="oxi-iheu-import-dialog" hidden>
            <div class="oxi-iheu-set-dialog-backdrop" data-oxi-iheu-close></div>
            <form class="oxi-iheu-set-dialog-box" method="post" enctype="multipart/form-data" role="dialog" aria-modal="true" aria-labelledby="oxi-iheu-import-title">
                <span class="oxi-iheu-set-dialog-icon is-brand dashicons dashicons-upload" aria-hidden="true"></span>
                <h2 class="oxi-iheu-set-dialog-title" id="oxi-iheu-import-title"><?php esc_html_e( 'Import a shortcode', 'image-hover-effects-ultimate' ); ?></h2>
                <p class="oxi-iheu-set-dialog-text"><?php esc_html_e( 'Choose a JSON file exported from Image Hover Effects. It is added as a new shortcode, nothing is replaced.', 'image-hover-effects-ultimate' ); ?></p>
                <label class="oxi-iheu-sc-drop">
                    <span class="dashicons dashicons-media-code" aria-hidden="true"></span>
                    <span class="oxi-iheu-sc-drop-text" data-empty="<?php esc_attr_e( 'Choose a .json file', 'image-hover-effects-ultimate' ); ?>"><?php esc_html_e( 'Choose a .json file', 'image-hover-effects-ultimate' ); ?></span>
                    <input type="file" name="importimagehoverultimatefile" accept=".json,application/json" required>
                </label>
                <?php wp_nonce_field( 'image-hover-effects-ultimate-import' ); ?>
                <div class="oxi-iheu-set-dialog-actions">
                    <button type="button" class="oxi-iheu-set-btn is-secondary" data-oxi-iheu-close><?php esc_html_e( 'Cancel', 'image-hover-effects-ultimate' ); ?></button>
                    <button type="submit" class="oxi-iheu-set-btn is-primary" name="importdatasubmit" value="Save"><?php esc_html_e( 'Import', 'image-hover-effects-ultimate' ); ?></button>
                </div>
            </form>
        </div>

        <div class="oxi-iheu-set-dialog" id="oxi-iheu-clone-dialog" hidden>
            <div class="oxi-iheu-set-dialog-backdrop" data-oxi-iheu-close></div>
            <form class="oxi-iheu-set-dialog-box" id="oxi-iheu-clone-form" role="dialog" aria-modal="true" aria-labelledby="oxi-iheu-clone-title">
                <span class="oxi-iheu-set-dialog-icon is-brand dashicons dashicons-admin-page" aria-hidden="true"></span>
                <h2 class="oxi-iheu-set-dialog-title" id="oxi-iheu-clone-title"><?php esc_html_e( 'Clone shortcode', 'image-hover-effects-ultimate' ); ?></h2>
                <p class="oxi-iheu-set-dialog-text"><?php esc_html_e( 'Creates a copy with the same design and image items, then opens it in the editor.', 'image-hover-effects-ultimate' ); ?></p>
                <label class="oxi-iheu-set-dialog-label" for="oxi-iheu-clone-name"><?php esc_html_e( 'Name for the copy', 'image-hover-effects-ultimate' ); ?></label>
                <input type="text" class="oxi-iheu-set-input" id="oxi-iheu-clone-name" required autocomplete="off" data-suffix="<?php esc_attr_e( 'copy', 'image-hover-effects-ultimate' ); ?>">
                <input type="hidden" id="oxi-iheu-clone-id" value="">
                <p class="oxi-iheu-set-dialog-status" role="status" aria-live="polite"
                    data-saving="<?php esc_attr_e( 'Cloning', 'image-hover-effects-ultimate' ); ?>"
                    data-error="<?php esc_attr_e( 'Could not clone, try again.', 'image-hover-effects-ultimate' ); ?>"></p>
                <div class="oxi-iheu-set-dialog-actions">
                    <button type="button" class="oxi-iheu-set-btn is-secondary" data-oxi-iheu-close><?php esc_html_e( 'Cancel', 'image-hover-effects-ultimate' ); ?></button>
                    <button type="submit" class="oxi-iheu-set-btn is-primary"><?php esc_html_e( 'Clone', 'image-hover-effects-ultimate' ); ?></button>
                </div>
            </form>
        </div>

        <div class="oxi-iheu-set-dialog" id="oxi-iheu-delete-sc-dialog" hidden>
            <div class="oxi-iheu-set-dialog-backdrop" data-oxi-iheu-close></div>
            <div class="oxi-iheu-set-dialog-box" role="alertdialog" aria-modal="true" aria-labelledby="oxi-iheu-delete-sc-title" aria-describedby="oxi-iheu-delete-sc-desc">
                <span class="oxi-iheu-set-dialog-icon dashicons dashicons-trash" aria-hidden="true"></span>
                <h2 class="oxi-iheu-set-dialog-title" id="oxi-iheu-delete-sc-title" data-template="<?php /* translators: %s: shortcode name */ esc_attr_e( 'Delete %s?', 'image-hover-effects-ultimate' ); ?>"></h2>
                <p class="oxi-iheu-set-dialog-text" id="oxi-iheu-delete-sc-desc">
                    <?php esc_html_e( 'Any page that still uses this shortcode will show nothing in its place. Its design and image items are deleted for good.', 'image-hover-effects-ultimate' ); ?>
                </p>
                <p class="oxi-iheu-sc-dialog-code"><code></code></p>
                <p class="oxi-iheu-set-dialog-status" role="status" aria-live="polite"
                    data-saving="<?php esc_attr_e( 'Deleting', 'image-hover-effects-ultimate' ); ?>"
                    data-error="<?php esc_attr_e( 'Could not delete, try again.', 'image-hover-effects-ultimate' ); ?>"></p>
                <div class="oxi-iheu-set-dialog-actions">
                    <button type="button" class="oxi-iheu-set-btn is-secondary" data-oxi-iheu-close><?php esc_html_e( 'Cancel', 'image-hover-effects-ultimate' ); ?></button>
                    <button type="button" class="oxi-iheu-set-btn is-danger-solid" id="oxi-iheu-delete-sc-submit"><?php esc_html_e( 'Delete shortcode', 'image-hover-effects-ultimate' ); ?></button>
                </div>
            </div>
        </div>
        <?php
    }

    private function get_export_link( $template_id ) {

        return add_query_arg(
            [
                'action' => 'image_hover_settings',
                'functionname' => 'shortcode_export',
                '_wpnonce' => wp_create_nonce( 'image_hover_ultimate' ),
                'rawdata' => 'Get Export Data',
                'styleid' => $template_id,
            ],
            admin_url( 'admin-ajax.php' ),
        );
    }



    public function CSSJS_load() {
        $this->manual_import_style();
        $this->admin_js();
        $this->admin_home();
        $this->admin_rest_api();
        apply_filters( 'oxi-image-hover-plugin/admin_menu', true );
    }

    /**
     * Admin Notice JS file loader
     * @return void
     */
    public function admin_rest_api() {
        wp_enqueue_script( 'oxi-image-hover-shortcode', OXI_IMAGE_HOVER_URL . 'assets/backend/js/shortcode.js', [ 'jquery', 'jquery.dataTables.min' ], filemtime( OXI_IMAGE_HOVER_PATH . 'assets/backend/js/shortcode.js' ), true );
    }

    /**
     * Plugin Name Convert to View
     *
     * @since 9.3.0
     */
    public function name_( $data ) {
        $data = str_replace( '_', ' ', $data );
        $data = str_replace( '-', ' ', $data );
        $data = str_replace( '+', ' ', $data );
        echo esc_html( ucwords( $data ) );
    }

	public function database_data() {
		global $wpdb;
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results( 'SELECT * FROM ' . esc_sql( $this->parent_table ) . ' ORDER BY id DESC', ARRAY_A );
	}

    /**
     * Generate safe path
     * @since v1.0.0
     */
    public function safe_path( $path ) {

        $path = str_replace( [ '//', '\\\\' ], [ '/', '\\' ], $path );
        return str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $path );
    }

    public function manual_import_style() {
		// Make sure the request is POST
		if ( ! empty( $_POST['importdatasubmit'] ) ) {

			// Unsplash and sanitize the submit button
			$import_submit = sanitize_text_field( wp_unslash( $_POST['importdatasubmit'] ) );

			if ( $import_submit === 'Save' ) {

				// Nonce: unslash and sanitize
				$nonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';

				if ( ! wp_verify_nonce( $nonce, 'image-hover-effects-ultimate-import' ) ) {
					wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'image-hover-effects-ultimate' ) );
				}

				// Check if file is uploaded
				if ( ! empty( $_FILES['importimagehoverultimatefile'] )
					&& isset( $_FILES['importimagehoverultimatefile']['name'], $_FILES['importimagehoverultimatefile']['tmp_name'] ) ) {
					$filename = sanitize_file_name( $_FILES['importimagehoverultimatefile']['name'] );
					$tmp_name = sanitize_text_field( wp_unslash( $_FILES['importimagehoverultimatefile']['tmp_name'] ) );

					if ( ! current_user_can( 'upload_files' ) ) {
						wp_die( esc_html__( 'You do not have permission to upload files.', 'image-hover-effects-ultimate' ) );
					}

					$allowedMimes = [ 'json' => 'application/json' ];
					$fileInfo     = wp_check_filetype( $filename, $allowedMimes );

					if ( empty( $fileInfo['ext'] ) ) {
						wp_die( esc_html__( 'You can only upload JSON files.', 'image-hover-effects-ultimate' ) );
					}

					$content = json_decode( file_get_contents( $tmp_name ), true );

					if ( empty( $content ) || ! isset( $content['style'] ) || ! is_array( $content['style'] ) ) {
						return new \WP_Error( 'file_error', esc_html__( 'Invalid content in file.', 'image-hover-effects-ultimate' ) );
					}

					$ImportApi = new \OXI_IMAGE_HOVER_PLUGINS\Classes\ImageApi();
					$new_slug  = $ImportApi->post_json_import( $content );

					echo '<script type="text/javascript">document.location.href = ' . wp_json_encode( $new_slug ) . ';</script>';
					exit;
				}
			}
		}
	}

    /**
     * Number of image items per shortcode.
     *
     * @since 9.12.0
     *
     * @return array<int,int>
     */
    public function item_counts() {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $rows   = $wpdb->get_results( 'SELECT styleid, COUNT(*) AS total FROM ' . esc_sql( $this->child_table ) . ' GROUP BY styleid', ARRAY_A );
        $counts = [];
        foreach ( (array) $rows as $row ) {
            $counts[ (int) $row['styleid'] ] = (int) $row['total'];
        }
        return $counts;
    }

    public function created_shortcode( $rows = [] ) {
        $counts = $this->item_counts();
        ?>
        <section class="oxi-iheu-set-card oxi-iheu-sc-card">
            <table class="oxi_addons_table_data oxi-iheu-sc-table">
                <thead>
                    <tr>
                        <th scope="col"><?php esc_html_e( 'Name', 'image-hover-effects-ultimate' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Shortcode', 'image-hover-effects-ultimate' ); ?></th>
                        <th scope="col" class="oxi-iheu-sc-actions-col"><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'image-hover-effects-ultimate' ); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ( $rows as $value ) {
                        $id        = (int) $value['id'];
                        $effects   = $this->effects_converter( $value['style_name'] );
                        $name      = ucwords( str_replace( [ '_', '-', '+' ], ' ', $value['name'] ) );
                        $template  = ucwords( str_replace( [ '_', '-', '+' ], ' ', $value['style_name'] ) );
                        $shortcode = '[iheu_ultimate_oxi id="' . $id . '"]';
                        $php       = "<?php echo do_shortcode('" . $shortcode . "'); ?>";
                        $items     = isset( $counts[ $id ] ) ? $counts[ $id ] : 0;
                        $edit_url  = admin_url( "admin.php?page=oxi-image-hover-ultimate&effects=$effects&styleid=$id" );
                        ?>
                        <tr data-id="<?php echo esc_attr( $id ); ?>" data-name="<?php echo esc_attr( $name ); ?>">
                            <td class="oxi-iheu-sc-name" data-order="<?php echo esc_attr( $id ); ?>">
                                <a class="oxi-iheu-sc-title" href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( $name ); ?></a>
                                <span class="oxi-iheu-sc-meta">
                                    <span class="oxi-iheu-sc-id">#<?php echo esc_html( $id ); ?></span>
                                    <span class="oxi-iheu-sc-template"><?php echo esc_html( $template ); ?></span>
                                    <?php if ( $items > 0 ) : ?>
                                        <span><?php echo esc_html( sprintf( /* translators: %s: number of image items */ _n( '%s item', '%s items', $items, 'image-hover-effects-ultimate' ), number_format_i18n( $items ) ) ); ?></span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="oxi-iheu-sc-code-cell">
                                <div class="oxi-iheu-sc-code">
                                    <code><?php echo esc_html( $shortcode ); ?></code>
                                    <button type="button" class="oxi-iheu-sc-copy" data-copy="<?php echo esc_attr( $shortcode ); ?>" aria-label="<?php esc_attr_e( 'Copy shortcode', 'image-hover-effects-ultimate' ); ?>">
                                        <span class="dashicons dashicons-admin-page" aria-hidden="true"></span><span class="oxi-iheu-sc-copy-text"><?php esc_html_e( 'Copy', 'image-hover-effects-ultimate' ); ?></span>
                                    </button>
                                </div>
                                <button type="button" class="oxi-iheu-sc-copy-php" data-copy="<?php echo esc_attr( $php ); ?>">
                                    <span class="oxi-iheu-sc-copy-text"><?php esc_html_e( 'Copy PHP code', 'image-hover-effects-ultimate' ); ?></span>
                                </button>
                            </td>
                            <td class="oxi-iheu-sc-actions">
                                <div class="oxi-iheu-sc-actions-wrap">
                                    <a class="oxi-iheu-sc-action is-edit" href="<?php echo esc_url( $edit_url ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: shortcode name */ __( 'Edit %s', 'image-hover-effects-ultimate' ), $name ) ); ?>">
                                        <span class="dashicons dashicons-edit" aria-hidden="true"></span><?php esc_html_e( 'Edit', 'image-hover-effects-ultimate' ); ?>
                                    </a>
                                    <button type="button" class="oxi-iheu-sc-action is-clone oxi-iheu-sc-clone" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: shortcode name */ __( 'Clone %s', 'image-hover-effects-ultimate' ), $name ) ); ?>">
                                        <span class="dashicons dashicons-admin-page" aria-hidden="true"></span><?php esc_html_e( 'Clone', 'image-hover-effects-ultimate' ); ?>
                                    </button>
                                    <a class="oxi-iheu-sc-action is-export" href="<?php echo esc_url( $this->get_export_link( $id ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: shortcode name */ __( 'Export %s', 'image-hover-effects-ultimate' ), $name ) ); ?>">
                                        <span class="dashicons dashicons-download" aria-hidden="true"></span><?php esc_html_e( 'Export', 'image-hover-effects-ultimate' ); ?>
                                    </a>
                                    <button type="button" class="oxi-iheu-sc-action is-delete oxi-iheu-sc-delete" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: shortcode name */ __( 'Delete %s', 'image-hover-effects-ultimate' ), $name ) ); ?>">
                                        <span class="dashicons dashicons-trash" aria-hidden="true"></span><?php esc_html_e( 'Delete', 'image-hover-effects-ultimate' ); ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </section>
        <?php
    }

}
