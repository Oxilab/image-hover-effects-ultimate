<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules;

class Elementor {
    public function __construct() {
        add_action( 'elementor/widgets/register', [ $this, 'register' ] );
        add_filter( 'elementor/widgets/black_list', [ $this, 'classic_widget_black_list' ] );
        add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_icon_style' ] );
        add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'editor_enqueue_scripts' ] );
    }

    public function editor_enqueue_scripts() {
        wp_enqueue_script(
            'iheu-elementor-edit-btn',
            OXI_IMAGE_HOVER_URL . 'assets/frontend/js/elementor-edit-btn.js',
            [ 'elementor-editor' ],
            OXI_IMAGE_HOVER_PLUGIN_VERSION,
            true
        );
    }

    public function editor_icon_style() {
        // The colored plugin logo, the same one WPBakery and the block show (dash-icon.svg is the white menu glyph,
        // which the Elementor panel showed as a grey circle).
        $icon_url = OXI_IMAGE_HOVER_URL . 'image/logo.png';
        echo '<style>
            i.icon-image-hover-ultimate::before { display: none !important; }
            i.icon-image-hover-ultimate {
                display: inline-block !important;
                width: 1em;
                height: 1em;
                background-image: url(' . esc_url( $icon_url ) . ');
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
                vertical-align: middle;
            }
        </style>';
    }

    public function register( $widgets_manager ) {
        if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
            return;
        }
        if ( ! class_exists( __NAMESPACE__ . '\Image_Hover_Elementor_Widget' ) ) {
            oxilab_define_image_hover_elementor_widget();
        }
        $widgets_manager->register( new Image_Hover_Elementor_Widget() );

        // The classic widget, registered again under its usual name (wp-widget-iheu_widget) so pages that already use
        // it keep working, but hidden from the panel and its search: the Image Hover widget above is the one to use.
        $key = $this->classic_widget_key();
        if ( '' !== $key && class_exists( '\Elementor\Widget_WordPress' ) ) {
            oxilab_define_image_hover_classic_elementor_widget();
            $widgets_manager->register( new Image_Hover_Classic_Elementor_Widget( [], [ 'widget_name' => $key ] ) );
        }
    }

    /**
     * Elementor lists every classic WordPress widget in its search, so the plugin showed up twice ("Image Hover" and
     * "Image Hover Effects Ultimate"). Leave the classic widget out of Elementor's own list; register() adds it back
     * hidden.
     *
     * @param array $black_list Keys of the WordPress widgets Elementor skips.
     * @return array
     */
    public function classic_widget_black_list( $black_list ) {
        $key = $this->classic_widget_key();
        if ( '' !== $key && is_array( $black_list ) ) {
            $black_list[] = $key;
        }
        return $black_list;
    }

    /**
     * The classic widget's key in the WordPress widget factory. It is registered as an object, so the key is the
     * object hash, not the class name.
     *
     * @return string Empty when the widget is not registered.
     */
    private function classic_widget_key() {
        global $wp_widget_factory;
        if ( ! $wp_widget_factory instanceof \WP_Widget_Factory ) {
            return '';
        }
        foreach ( $wp_widget_factory->widgets as $key => $widget ) {
            if ( $widget instanceof Widget ) {
                return (string) $key;
            }
        }
        return '';
    }
}
function oxilab_define_image_hover_classic_elementor_widget() {
    if ( class_exists( '\Elementor\Widget_WordPress' ) && ! class_exists( __NAMESPACE__ . '\Image_Hover_Classic_Elementor_Widget' ) ) {
        // Defined here, like the Image Hover widget below, because the parent class exists only when Elementor loads.
        // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
        class Image_Hover_Classic_Elementor_Widget extends \Elementor\Widget_WordPress {
            public function hide_on_search() {
                return true;
            }
            public function get_icon() {
                return 'icon-image-hover-ultimate';
            }
        }
    }
}
function oxilab_define_image_hover_elementor_widget() {
    if ( class_exists( '\Elementor\Widget_Base' ) && ! class_exists( __NAMESPACE__ . '\Image_Hover_Elementor_Widget' ) ) {
        class Image_Hover_Elementor_Widget extends \Elementor\Widget_Base {
            public function get_name() { return 'iheu_image_hover'; }
            public function get_title() { return 'Image Hover'; }
            public function get_icon() { return 'icon-image-hover-ultimate'; }
            public function get_categories() { return [ 'general' ]; }
            public function get_style_depends() {
                return [
                    'oxi-animation', 'oxi-image-hover',
                    'oxi-image-hover-carousel-swiper.min.css', 'oxi-image-hover-style-3',
                    'oxi-image-hover-filter-style-1', 'oxi-image-hover-filter-style-2',
                    'oxi-image-hover-comparison-box', 'oxi-image-hover-comparison-style-1', 'oxi-addons-main-wrapper-image-comparison-style-1',
                    'oxi-image-hover-light-box', 'oxi-image-hover-light-style-1', 'image_zoom.css',
                    'oxi-image-hover-glightbox',
                    'oxi-image-hover-display-style-1',
                ];
            }
            public function get_script_depends() {
                return [
                    'jquery', 'waypoints.min', 'oxi-image-hover',
                    'oxi-image-carousel-swiper.min.js', 'oxi-iheu-elementor-carousel',
                    'imagesloaded.pkgd.min', 'jquery.isotope',
                    'jquery-event-move', 'jquery-twentytwenty',
                    'image_zoom',
                    'oxi-image-hover-glightbox',
                    'oxi_image_style_1_loader',
                ];
            }

            protected function register_controls() {
                global $wpdb;
                $options   = [];
                $edit_urls = [];
                $table     = $wpdb->prefix . 'image_hover_ultimate_style';
                $rows      = $wpdb->get_results( "SELECT id, name, style_name FROM {$table} ORDER BY id DESC", ARRAY_A );
                if ( $rows ) {
                    foreach ( $rows as $row ) {
                        $label = ( isset( $row['name'] ) && $row['name'] !== '' ? $row['name'] : 'Image Hover' ) . ' (#' . $row['id'] . ')';
                        $options[ (string) $row['id'] ] = $label;

                        $parts      = explode( '-', $row['style_name'] );
                        $effects    = isset( $parts[0] ) ? strtolower( $parts[0] ) : '';
                        $edit_urls[ (string) $row['id'] ] = admin_url(
                            'admin.php?page=oxi-image-hover-ultimate&effects=' . $effects . '&styleid=' . $row['id']
                        );
                    }
                }
                $default = '';
                if ( ! empty( $options ) ) {
                    $keys    = array_keys( $options );
                    $default = reset( $keys );
                }

                $this->start_controls_section( 'section_image_hover', [ 'label' => 'Image Hover' ] );

                $this->add_control( 'id', [
                    'label'   => 'Shortcode',
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'options' => $options,
                    'default' => $default,
                ] );

                // Edit button — URL is managed by elementor-edit-btn.js via
                // Elementor's native panel/open_editor hook + model change event.
                $edit_urls_json = esc_attr( wp_json_encode( $edit_urls ) );
                $this->add_control( 'edit_shortcode_btn', [
                    'type'      => \Elementor\Controls_Manager::RAW_HTML,
                    'raw'       => '<a class="iheu-edit-shortcode-btn" href="#" target="_blank"
                                        data-urls="' . $edit_urls_json . '"
                                        style="display:inline-flex;align-items:center;gap:6px;margin-top:4px;
                                               padding:7px 18px;background:#f75186;color:#fff;border-radius:4px;
                                               text-decoration:none;font-size:13px;font-weight:600;
                                               transition:background .2s;"
                                        onmouseover="this.style.background=\'#e0406f\'"
                                        onmouseout="this.style.background=\'#f75186\'">
                                        ✏️ Edit Shortcode
                                    </a>',
                    'separator' => 'before',
                ] );

                $this->end_controls_section();
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $id = isset( $settings['id'] ) ? $settings['id'] : '';
                echo do_shortcode( '[iheu_ultimate_oxi id="' . esc_attr( $id ) . '"]' );
            }
        }
    }
}
