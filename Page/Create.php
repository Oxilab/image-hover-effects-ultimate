<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Page;

use OXI_IMAGE_HOVER_PLUGINS\Helper\CSS_JS_Loader;
use OXI_IMAGE_HOVER_PLUGINS\Helper\Public_Helper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Description of Create
 *
 * @author $biplob018
 */
class Create {

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

    /**
     * Define $wpdb
     *
     * @since 9.3.0
     */
    public $effects;

    /**
     * Define $wpdb
     *
     * @since 9.3.0
     */
    public $oxitype;
    public $TEMPLATE = [];
    public $pre_active = [];
    public $pre_clecked = [];
    public $activated_template = [];

    use Public_Helper;
    use CSS_JS_Loader;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->parent_table = $this->wpdb->prefix . 'image_hover_ultimate_style';
		$this->child_table = $this->wpdb->prefix . 'image_hover_ultimate_list';
		$this->import_table = $this->wpdb->prefix . 'oxi_div_import';

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->effects = isset( $_GET['effects'] ) ? sanitize_text_field( wp_unslash( $_GET['effects'] ) ) : '';

		$this->oxitype = $this->effects . '-ultimate';
		$this->CSSJS_load();
		$this->Render();
	}

    public function Render() {
        ?>
        <hr class="wp-header-end">
        <div class="oxi-iheu-settings oxi-iheu-create">
            <?php
            // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
            if ( ! ( isset( $_GET['effects'] ) && isset( $_GET['styleid'] ) ) ) {
                $this->Admin_header();
            }
            $this->template();
            $this->create_new();
            ?>
        </div>
        <?php
    }

    /**
     * The name dialog behind every "Use this design" button.
     *
     * Creating posts the same "create_new" request as before: the new
     * style's name and the path of the chosen design's layout file.
     *
     * @since 9.12.1
     */
    public function create_new() {
        ?>
        <div class="oxi-iheu-set-dialog" id="oxi-iheu-create-dialog" hidden>
            <div class="oxi-iheu-set-dialog-backdrop" data-oxi-iheu-close></div>
            <form class="oxi-iheu-set-dialog-box" id="oxi-iheu-create-form" role="dialog" aria-modal="true" aria-labelledby="oxi-iheu-create-title">
                <span class="oxi-iheu-set-dialog-icon is-brand dashicons dashicons-plus-alt2" aria-hidden="true"></span>
                <h2 class="oxi-iheu-set-dialog-title" id="oxi-iheu-create-title"><?php esc_html_e( 'Create a new style', 'image-hover-effects-ultimate' ); ?></h2>
                <p class="oxi-iheu-set-dialog-text oxi-iheu-create-design"></p>
                <label class="oxi-iheu-set-dialog-label" for="oxi-iheu-create-name"><?php esc_html_e( 'Name', 'image-hover-effects-ultimate' ); ?></label>
                <input type="text" class="oxi-iheu-set-input" id="oxi-iheu-create-name" required autocomplete="off" maxlength="50" placeholder="<?php esc_attr_e( 'For example: Team gallery', 'image-hover-effects-ultimate' ); ?>">
                <input type="hidden" id="oxi-iheu-create-source" value="">
                <p class="oxi-iheu-set-dialog-status" role="status" aria-live="polite"
                    data-saving="<?php esc_attr_e( 'Creating', 'image-hover-effects-ultimate' ); ?>"
                    data-error="<?php esc_attr_e( 'Could not create it, try again.', 'image-hover-effects-ultimate' ); ?>"></p>
                <div class="oxi-iheu-set-dialog-actions">
                    <button type="button" class="oxi-iheu-set-btn is-secondary" data-oxi-iheu-close><?php esc_html_e( 'Cancel', 'image-hover-effects-ultimate' ); ?></button>
                    <button type="submit" class="oxi-iheu-set-btn is-primary"><?php esc_html_e( 'Create and edit', 'image-hover-effects-ultimate' ); ?></button>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Image Hover Ultimate Pre Active Check.
     *
     * @since 9.3.0
     */
    public function pre_active_check() {

		global $wpdb;

		// Fetch existing templates for this oxitype
		$template = $this->wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . esc_sql( $this->import_table ) . ' WHERE type = %s ORDER BY id DESC',
				$this->oxitype
			),
			ARRAY_A
		);

		if ( count( $template ) < 1 ) {
			// Insert default templates
			foreach ( $this->pre_active as $value ) {
				$wpdb->query(
					$wpdb->prepare(
						'INSERT INTO ' . esc_sql( $this->import_table ) . ' (type, name) VALUES (%s, %s)',
						$this->oxitype,
						$value
					)
				);
			}
			$this->activated_template = $this->pre_clecked;
		} else {
			foreach ( $template as $value ) {
				$this->activated_template[ $value['name'] ] = $value['name'];
			}
		}
	}

	public function JSON_DATA() {

	}

	/**
	 * The effect type's name, as on its card on the Image Hover page.
	 *
	 * @since 9.12.1
	 * @return string
	 */
	public function effect_title() {
		$names = [
			'magnifier'  => __( 'Image Magnifier', 'image-hover-effects-ultimate' ),
			'comparison' => __( 'Image Comparison', 'image-hover-effects-ultimate' ),
			'lightbox'   => __( 'Image Lightbox', 'image-hover-effects-ultimate' ),
			'display'    => __( 'Display Post', 'image-hover-effects-ultimate' ),
			'carousel'   => __( 'Carousel Slider', 'image-hover-effects-ultimate' ),
			'filter'     => __( 'Filter & Sorting', 'image-hover-effects-ultimate' ),
		];
		if ( isset( $names[ $this->effects ] ) ) {
			return $names[ $this->effects ];
		}
		/* translators: %s: effect type, for example Button or General */
		return sprintf( __( '%s Effects', 'image-hover-effects-ultimate' ), ucfirst( $this->effects ) );
	}

	public function Admin_header() {
		apply_filters( 'oxi-image-hover-support-and-comments', true );
		?>
        <header class="oxi-iheu-set-hero">
            <img class="oxi-iheu-set-hero-logo" src="<?php echo esc_url( OXI_IMAGE_HOVER_URL . 'image/logo.png' ); ?>" alt="" width="52" height="52">
            <div class="oxi-iheu-set-hero-text">
                <h1 class="oxi-iheu-set-title"><?php echo esc_html( $this->effect_title() ); ?></h1>
                <p class="oxi-iheu-set-subtitle"><?php esc_html_e( 'Pick a design you like, give it a name, then customize it in the editor.', 'image-hover-effects-ultimate' ); ?></p>
            </div>
            <a class="oxi-iheu-set-btn is-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-image-hover-ultimate' ) ); ?>">
                <span class="dashicons dashicons-arrow-left-alt" aria-hidden="true"></span><?php esc_html_e( 'All effect types', 'image-hover-effects-ultimate' ); ?>
            </a>
        </header>
		<?php
	}

	/**
	 * One card per layout, one "Use this design" per design in it.
	 *
	 * Free sites can use the free layouts (pre_active); the others keep their
	 * Pro button, as before. Each design's hidden input holds the path of its
	 * layout file, which is what "create_new" receives.
	 *
	 * @since 9.3.0
	 */
	public function template() {
		$is_pro = apply_filters( 'oxi-image-hover-plugin-version', false ) == true;
		?>
        <div class="oxi-iheu-tpl-list">
            <?php
            foreach ( $this->TEMPLATE as $key => $value ) {
                $title = ucfirst( $this->effects ) . ' ' . $key;
                $free  = $is_pro || array_key_exists( $this->effects . '-' . $key, $this->pre_clecked );
                $designs = [];
                $i = 1;
                foreach ( $value as $filename ) {
                    $folder = OXI_IMAGE_HOVER_PATH . 'Modules/' . ucfirst( $this->effects ) . '/Layouts/' . $key . '/';
                    if ( is_file( $folder . $filename ) ) {
                        $designs[ $i ] = $filename;
                        ++$i;
                    }
                }
                if ( ! $designs ) {
                    continue;
                }
                ?>
                <section class="oxi-iheu-set-card oxi-iheu-tpl" id="<?php echo esc_attr( $this->effects ) . '-' . esc_attr( $key ); ?>">
                    <div class="oxi-iheu-tpl-head">
                        <div class="oxi-iheu-tpl-title">
                            <h2><?php echo esc_html( $title ); ?></h2>
                            <span class="oxi-iheu-tpl-count"><?php echo esc_html( sprintf( /* translators: %s: number of designs */ _n( '%s design', '%s designs', count( $designs ), 'image-hover-effects-ultimate' ), number_format_i18n( count( $designs ) ) ) ); ?></span>
                        </div>
                        <?php if ( ! $free ) : ?>
                            <span class="oxi-iheu-tpl-pro"><span class="dashicons dashicons-lock" aria-hidden="true"></span><?php esc_html_e( 'Pro layout', 'image-hover-effects-ultimate' ); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="oxi-iheu-tpl-grid is-<?php echo (int) min( 3, count( $designs ) ); ?>">
                        <?php
                        foreach ( $designs as $n => $filename ) {
                            $folder = OXI_IMAGE_HOVER_PATH . 'Modules/' . ucfirst( $this->effects ) . '/Layouts/' . $key . '/';
                            $params = json_decode( file_get_contents( $folder . $filename ), true );
                            $s      = explode( '-', $params['style']['style_name'] );
                            $source = 'oxi-template-' . $key . '-data-' . $n;
                            /* translators: 1: layout name, 2: design number */
                            $label  = sprintf( __( '%1$s, design %2$s', 'image-hover-effects-ultimate' ), $title, $n );
                            $CLASS  = 'OXI_IMAGE_HOVER_PLUGINS\Modules\\' . ucfirst( $s[0] ) . '\Render\Effects' . $s[1];
                            ?>
                            <div class="oxi-iheu-tpl-item">
                                <div class="oxi-iheu-tpl-preview">
                                    <div class="oxi-iheu-tpl-render">
                                        <?php
                                        if ( class_exists( $CLASS ) ) :
                                            new $CLASS( $params['style'], $params['child'] );
                                        endif;
                                        ?>
                                    </div>
                                    <input type="hidden" id="<?php echo esc_attr( $source ); ?>" value="Modules/<?php echo esc_attr( ucfirst( $this->effects ) ); ?>/Layouts/<?php echo esc_attr( $key ); ?>/<?php echo esc_attr( $filename ); ?>">
                                </div>
                                <div class="oxi-iheu-tpl-foot">
                                    <span class="oxi-iheu-tpl-design"><?php echo esc_html( sprintf( /* translators: %s: design number */ __( 'Design %s', 'image-hover-effects-ultimate' ), $n ) ); ?></span>
                                    <?php if ( $free ) : ?>
                                        <button type="button" class="oxi-iheu-set-btn is-primary is-sm oxi-iheu-tpl-use" data-source="<?php echo esc_attr( $source ); ?>" data-label="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Use this design', 'image-hover-effects-ultimate' ); ?></button>
                                    <?php else : ?>
                                        <button type="button" class="oxi-iheu-set-btn is-secondary is-sm oxi-premium-modal-trigger" name="styleactive<?php echo esc_attr( $key ); ?>"><span class="dashicons dashicons-lock" aria-hidden="true"></span><?php esc_html_e( 'Unlock', 'image-hover-effects-ultimate' ); ?></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </section>
                <?php
            }
            ?>
        </div>
		<?php
	}



	public function rec_listFiles( $from = '.' ) {
		if ( ! is_dir( $from ) ) {
			return false;
		}

		$files = [];
		if ( $dh = opendir( $from ) ) {
			while ( false !== ( $file = readdir( $dh ) ) ) {
				// Skip '.' and '..'
				if ( $file == '.' || $file == '..' ) {
					continue;
				}
				$path = $from . '/' . $file;
				if ( is_dir( $path ) ) {
					$files += $this->rec_listFiles( $path );
				} else {
					if ( strpos( $path, 'json' ) !== false ) {
						$folder = explode( '/Layouts/', $path );
						$folder = explode( '/', $folder[1] );
					}
					if ( isset( $folder[1] ) ) {
						$files[ $folder[0] ][] = $folder[1];
					}
				}
			}
			closedir( $dh );
		}
		ksort( $files );
		return $files;
	}

	public function CSSJS_load() {

		$this->JSON_DATA();
		$this->pre_clecked = array_flip( $this->pre_active );
		$this->admin_js();
		$this->admin_rest_api();
		$this->pre_active_check();
		apply_filters( 'oxi-image-hover-plugin/admin_menu', true );
	}

	/**
	 * Admin Notice JS file loader
	 * @return void
	 */
	public function admin_rest_api() {
		wp_enqueue_script( 'oxi-image-hover-create', OXI_IMAGE_HOVER_URL . 'assets/backend/js/create.js', [ 'jquery' ], filemtime( OXI_IMAGE_HOVER_PATH . 'assets/backend/js/create.js' ), true );
	}
}
