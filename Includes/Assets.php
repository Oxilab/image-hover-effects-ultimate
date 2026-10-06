<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Includes;

/**
 * Assets Handler Class
 *
 * @since 2.10.1
 */
class Assets {

	/**
	 * Assets class constructor
	 *
	 * @since 2.10.1
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scriptss' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'public_enqueue_scripts' ] );
		add_action( 'wp_footer', [ $this, 'print_late_styles' ], 5 );

		// Register carousel scripts globally for Elementor
		add_action( 'wp_enqueue_scripts', [ $this, 'register_swiper_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_swiper_scripts' ] );

		if ( class_exists( '\\Elementor\\Plugin' ) ) {
			add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_enqueue_styles' ] );
			add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'editor_enqueue_scripts' ] );
			add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'public_enqueue_scripts' ] );
			add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'public_enqueue_scripts' ] );
			add_action( 'elementor/preview/enqueue_styles', [ $this, 'editor_enqueue_styles' ] );
			add_action( 'elementor/preview/enqueue_scripts', [ $this, 'editor_enqueue_scripts' ] );
		}
	}

	public function editor_enqueue_styles() {
		$v = OXI_IMAGE_HOVER_PLUGIN_VERSION;
		$u = OXI_IMAGE_HOVER_URL;

		wp_enqueue_style( 'oxi-animation',   $u . 'assets/frontend/css/animation.css', [], $v );
		wp_enqueue_style( 'oxi-image-hover', $u . 'assets/frontend/css/style.css',     [], $v );

		wp_enqueue_style( 'oxi-image-hover-carousel-swiper.min.css',          $u . 'Modules/Carousel/Files/swiper.min.css',        [], $v );
		wp_enqueue_style( 'oxi-image-hover-style-3',                          $u . 'Modules/Carousel/Files/style-3.css',           [], $v );
		wp_enqueue_style( 'oxi-image-hover-filter-style-1',                   $u . 'Modules/Filter/Files/style-1.css',             [], $v );
		wp_enqueue_style( 'oxi-image-hover-filter-style-2',                   $u . 'Modules/Filter/Files/style-2.css',             [], $v );
		wp_enqueue_style( 'oxi-image-hover-comparison-box',                   $u . 'Modules/Comparison/Files/Comparison.css',      [], $v );
		wp_enqueue_style( 'oxi-image-hover-comparison-style-1',               $u . 'Modules/Comparison/Files/style-1.css',         [], $v );
		wp_enqueue_style( 'oxi-addons-main-wrapper-image-comparison-style-1', $u . 'Modules/Comparison/Files/twentytwenty.css',    [], $v );
		wp_enqueue_style( 'oxi-image-hover-light-box',                        $u . 'Modules/Magnifier/Files/Magnifier.css',        [], $v );
		wp_enqueue_style( 'oxi-image-hover-light-style-1',                    $u . 'Modules/Magnifier/Files/style-1.css',          [], $v );
		wp_enqueue_style( 'image_zoom.css',                                   $u . 'Modules/Magnifier/Files/image_zoom.min.css',   [], $v );
		wp_enqueue_style( 'oxi-image-hover-glightbox',                        $u . 'Modules/Lightbox/Files/glightbox.min.css',     [], $v );
		wp_enqueue_style( 'oxi-image-hover-lightbox-box',                     $u . 'Modules/Lightbox/Files/Lightbox.css',          [], $v );
		wp_enqueue_style( 'oxi-image-hover-lightbox-style-1',                 $u . 'Modules/Lightbox/Files/style-1.css',           [], $v );
		wp_enqueue_style( 'oxi-image-hover-lightbox-style-2',                 $u . 'Modules/Lightbox/Files/style-2.css',           [], $v );
		wp_enqueue_style( 'OxiPopup',                                         $u . 'Modules/Lightbox/Files/Popup.css',             [], $v );
		wp_enqueue_style( 'oxi-image-hover-display-style-1',                  $u . 'Modules/Display/Files/style-1.css',            [], $v );
	}

	public function editor_enqueue_scripts() {
		$v = OXI_IMAGE_HOVER_PLUGIN_VERSION;
		$u = OXI_IMAGE_HOVER_URL;

		wp_enqueue_script( 'jquery' );

		$deps = [ 'jquery' ];
		if ( get_option( 'oxi_addons_way_points' ) !== 'no' ) {
			wp_enqueue_script( 'waypoints.min', $u . 'assets/frontend/js/waypoints.min.js', [ 'jquery' ], $v, true );
			$deps[] = 'waypoints.min';
		}
		if ( get_option( 'image_hover_ultimate_mobile_device_key' ) !== 'normal' ) {
			wp_enqueue_script( 'oxi-image-hover-touch', $u . 'assets/frontend/js/touch.js', [ 'jquery' ], $v, true );
		}

		// Base scripts
		wp_enqueue_script( 'oxi-image-hover',              $u . 'assets/frontend/js/oxi-jquery.js',                 $deps,                                          $v, true );

		// Carousel
		wp_enqueue_script( 'oxi-image-carousel-swiper.min.js', $u . 'Modules/Carousel/Files/swiper.min.js',         [ 'jquery' ],                                   $v, true );
		wp_enqueue_script( 'oxi-iheu-elementor-carousel',  $u . 'assets/frontend/js/elementor-carousel-init.js',    [ 'jquery', 'oxi-image-carousel-swiper.min.js' ], $v, true );

		// Filter
		wp_enqueue_script( 'imagesloaded.pkgd.min',        $u . 'Modules/Filter/Files/imagesloaded.pkgd.min.js',    [ 'jquery' ],                                   $v, true );
		wp_enqueue_script( 'jquery.isotope',                $u . 'Modules/Filter/Files/jquery.isotope.js',           [ 'jquery', 'imagesloaded.pkgd.min' ],          $v, true );

		// Comparison
		wp_enqueue_script( 'jquery-event-move',             $u . 'Modules/Comparison/Files/jquery-event-move.js',   [ 'jquery' ],                                   $v, true );
		wp_enqueue_script( 'jquery-twentytwenty',           $u . 'Modules/Comparison/Files/jquery-twentytwenty.js', [ 'jquery', 'jquery-event-move' ],              $v, true );

		// Magnifier
		wp_enqueue_script( 'image_zoom',                    $u . 'Modules/Magnifier/Files/image_zoom.min.js',        [ 'jquery' ],                                   $v, true );

		// Lightbox
		wp_enqueue_script( 'oxi-image-hover-glightbox',    $u . 'Modules/Lightbox/Files/glightbox.min.js',          [ 'jquery' ],                                   $v, true );
		wp_enqueue_script( 'OxiPopup',                     $u . 'Modules/Lightbox/Files/Popup.js',                  [ 'jquery' ],                                   $v, true );

		// Display
		wp_enqueue_script( 'oxi_image_style_1_loader',     $u . 'Modules/Display/Files/style-1-loader.js',          [ 'jquery' ],                                   $v, true );
	}

	/**
	 * Method admin_enqueue_scriptss.
	 *
	 * @since 2.10.1
	 */
	public function admin_enqueue_scriptss() {
		$current_screen = get_current_screen()->id;
		$current_page   = isset( $_GET['page'] ) && $_GET['page'] ? $_GET['page'] : '';

		wp_enqueue_style( 'image-hover-ultimate-global-admin-style', OXI_IMAGE_HOVER_URL . 'assets/backend/css/global-admin.css', false, filemtime( OXI_IMAGE_HOVER_PATH . 'assets/backend/css/global-admin.css' ) );

		if ( 'oxi-image-hover-ultimate' === $current_page || 'oxi-image-hover-shortcode' === $current_page || 'oxi-image-hover-ultimate-settings' === $current_page ) {
			$this->loader_font_familly_validation( [ 'Bree+Serif', 'Source+Sans+Pro' ] );
			wp_enqueue_style( 'oxilab-image-hover-bootstraps', OXI_IMAGE_HOVER_URL . 'assets/backend/css/bootstrap.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION );
			wp_enqueue_style( 'font-awsome.mins', OXI_IMAGE_HOVER_URL . 'assets/frontend/css/font-awsome.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION );
			wp_enqueue_style( 'oxilab-image-hover-admin-css', OXI_IMAGE_HOVER_URL . 'assets/backend/css/admin.css', false, filemtime( OXI_IMAGE_HOVER_PATH . 'assets/backend/css/admin.css' ) );

            // Load single editor page CSS only on editor page
            // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
            if ( isset( $_GET['effects'] ) && isset( $_GET['styleid'] ) ) {
                wp_enqueue_style( 'oxilab-image-hover-single-editor-css', OXI_IMAGE_HOVER_URL . 'assets/backend/css/single_editor_page.css', false, filemtime( OXI_IMAGE_HOVER_PATH . 'assets/backend/css/single_editor_page.css' ) );
            }

            if ( 'oxi-image-hover-ultimate-settings' === $current_page ) {
                wp_enqueue_style( 'oxilab-image-hover-settings-css', OXI_IMAGE_HOVER_URL . 'assets/backend/css/settings.css', [ 'oxilab-image-hover-admin-css' ], filemtime( OXI_IMAGE_HOVER_PATH . 'assets/backend/css/settings.css' ) );
            }
		}

		if ( 'image-hover-ultimate-getting-started' === $current_page ) {
			//CSS
			wp_enqueue_style( 'image-hover-ultimate-admin-welcome', OXI_IMAGE_HOVER_URL . 'assets/backend/css/getting-started.css', false, filemtime( OXI_IMAGE_HOVER_PATH . 'assets/backend/css/getting-started.css' ) );
		}
	}

	public function loader_font_familly_validation( $data = [] ) {
        foreach ( $data as $value ) {
            wp_enqueue_style( '' . $value . '', 'https://fonts.googleapis.com/css?family=' . $value . '', false, OXI_IMAGE_HOVER_PLUGIN_VERSION );
        }
    }

	/**
	 * Method public_enqueue_scripts.
	 *
	 * @since 2.10.1
	 */
	public function public_enqueue_scripts() {
	}

	public function register_swiper_scripts() {
		$v = OXI_IMAGE_HOVER_PLUGIN_VERSION;
		$u = OXI_IMAGE_HOVER_URL;

		// Base styles & scripts – must be registered so Elementor's
		// get_style_depends / get_script_depends can resolve them
		// during AJAX widget re-renders in the editor.
		wp_register_style( 'oxi-animation', $u . 'assets/frontend/css/animation.css', [], $v );
		wp_register_style( 'oxi-image-hover', $u . 'assets/frontend/css/style.css', [], $v );
		wp_register_script( 'waypoints.min', $u . 'assets/frontend/js/waypoints.min.js', [ 'jquery' ], $v, true );
		wp_register_script( 'oxi-image-hover', $u . 'assets/frontend/js/oxi-jquery.js', [ 'jquery' ], $v, true );

		// Carousel
		wp_register_script( 'oxi-image-carousel-swiper.min.js', $u . 'Modules/Carousel/Files/swiper.min.js', [ 'jquery' ], $v, true );
		wp_register_style( 'oxi-image-hover-carousel-swiper.min.css', $u . 'Modules/Carousel/Files/swiper.min.css', [], $v );
		wp_register_style( 'oxi-image-hover-style-3', $u . 'Modules/Carousel/Files/style-3.css', [], $v );
		wp_register_script( 'oxi-iheu-elementor-carousel', $u . 'assets/frontend/js/elementor-carousel-init.js', [ 'jquery', 'oxi-image-carousel-swiper.min.js' ], $v, true );


		// Filter
		wp_register_script( 'imagesloaded.pkgd.min', OXI_IMAGE_HOVER_URL . 'Modules/Filter/Files/imagesloaded.pkgd.min.js', [ 'jquery' ], OXI_IMAGE_HOVER_PLUGIN_VERSION, true );
		wp_register_script( 'jquery.isotope', OXI_IMAGE_HOVER_URL . 'Modules/Filter/Files/jquery.isotope.js', [ 'jquery', 'imagesloaded.pkgd.min' ], OXI_IMAGE_HOVER_PLUGIN_VERSION, true );
		wp_register_style( 'oxi-image-hover-filter-style-1', OXI_IMAGE_HOVER_URL . 'Modules/Filter/Files/style-1.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
		wp_register_style( 'oxi-image-hover-filter-style-2', OXI_IMAGE_HOVER_URL . 'Modules/Filter/Files/style-2.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );

		// Comparison
		wp_register_script( 'jquery-event-move', OXI_IMAGE_HOVER_URL . 'Modules/Comparison/Files/jquery-event-move.js', [ 'jquery' ], OXI_IMAGE_HOVER_PLUGIN_VERSION, true );
		wp_register_script( 'jquery-twentytwenty', OXI_IMAGE_HOVER_URL . 'Modules/Comparison/Files/jquery-twentytwenty.js', [ 'jquery', 'jquery-event-move' ], OXI_IMAGE_HOVER_PLUGIN_VERSION, true );
		wp_register_style( 'oxi-image-hover-comparison-box', OXI_IMAGE_HOVER_URL . 'Modules/Comparison/Files/Comparison.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
		wp_register_style( 'oxi-image-hover-comparison-style-1', OXI_IMAGE_HOVER_URL . 'Modules/Comparison/Files/style-1.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
		wp_register_style( 'oxi-addons-main-wrapper-image-comparison-style-1', OXI_IMAGE_HOVER_URL . 'Modules/Comparison/Files/twentytwenty.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );

		// Magnifier
		wp_register_script( 'image_zoom', OXI_IMAGE_HOVER_URL . 'Modules/Magnifier/Files/image_zoom.min.js', [ 'jquery' ], OXI_IMAGE_HOVER_PLUGIN_VERSION, true );
		wp_register_style( 'oxi-image-hover-light-box', OXI_IMAGE_HOVER_URL . 'Modules/Magnifier/Files/Magnifier.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
		wp_register_style( 'oxi-image-hover-light-style-1', OXI_IMAGE_HOVER_URL . 'Modules/Magnifier/Files/style-1.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
		wp_register_style( 'image_zoom.css', OXI_IMAGE_HOVER_URL . 'Modules/Magnifier/Files/image_zoom.min.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );

		// Lightbox
		wp_register_script( 'oxi-image-hover-glightbox', OXI_IMAGE_HOVER_URL . 'Modules/Lightbox/Files/glightbox.min.js', [ 'jquery' ], OXI_IMAGE_HOVER_PLUGIN_VERSION, true );
		wp_register_style( 'oxi-image-hover-glightbox', OXI_IMAGE_HOVER_URL . 'Modules/Lightbox/Files/glightbox.min.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
		wp_register_style( 'oxi-image-hover-lightbox-box', OXI_IMAGE_HOVER_URL . 'Modules/Lightbox/Files/Lightbox.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
		wp_register_style( 'oxi-image-hover-lightbox-style-1', OXI_IMAGE_HOVER_URL . 'Modules/Lightbox/Files/style-1.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
		wp_register_style( 'oxi-image-hover-lightbox-style-2', OXI_IMAGE_HOVER_URL . 'Modules/Lightbox/Files/style-2.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
		wp_register_style( 'OxiPopup', OXI_IMAGE_HOVER_URL . 'Modules/Lightbox/Files/Popup.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
		wp_register_script( 'OxiPopup', OXI_IMAGE_HOVER_URL . 'Modules/Lightbox/Files/Popup.js', [ 'jquery' ], OXI_IMAGE_HOVER_PLUGIN_VERSION, true );

		// Display
		wp_register_script( 'oxi_image_style_1_loader', OXI_IMAGE_HOVER_URL . 'Modules/Display/Files/style-1-loader.js', [ 'jquery' ], OXI_IMAGE_HOVER_PLUGIN_VERSION, true );
		wp_register_style( 'oxi-image-hover-display-style-1', OXI_IMAGE_HOVER_URL . 'Modules/Display/Files/style-1.css', [], OXI_IMAGE_HOVER_PLUGIN_VERSION );
	}

	public function print_late_styles() {
		$pending = \OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render::$pending_late_css;
		if ( empty( $pending ) ) {
			return;
		}
		\OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render::$pending_late_css = [];
		$css_output = '';
		foreach ( $pending as $entry ) {
			if ( ! empty( $entry['css'] ) ) {
				$css_output .= $entry['css'];
			}
		}
		if ( $css_output ) {
			$css_output = str_ireplace( '</style>', '', $css_output );
			echo '<style id="oxi-iheu-late-css">' . $css_output . '</style>';
		}
	}
}
