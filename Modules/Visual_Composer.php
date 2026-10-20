<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Description of Visual_Composer
 *
 * @author $biplob018
 */
class Visual_Composer {

    /**
     * Define $wpdb
     *
     * @since 9.3.0
     */
    public $wpdb;

    /**
     * Database Parent Table
     *
     * @since 9.3.0
     */
    public $parent_table;

    public function iheu_oxi_VC_shortcode( $atts ) {
        $atts = shortcode_atts(
            [
				'id' => '',
			], 
            $atts,
            'iheu_oxi_VC'
        );
        
        // Visual Composer passes the value, convert to integer
        $styleid = ! empty( $atts['id'] ) ? absint( $atts['id'] ) : 0;
        
        if ( empty( $styleid ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                return '<p style="color:red;">Visual Composer Image Hover: No Style ID selected</p>';
            }
            return '';
        }
        
        ob_start();
        if ( function_exists( 'oxilab_imagehover' ) && oxilab_imagehover() ) {
            oxilab_imagehover()->shortcode_render( $styleid, 'user' );
        } else {
            if ( current_user_can( 'manage_options' ) ) {
                echo '<p style="color:red;">Visual Composer Image Hover: Plugin not properly initialized</p>';
            }
        }
        return ob_get_clean();
    }

    public function iheu_oxi_VC_extension() {
		// Check if vc_map function exists (Visual Composer is active)
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}
		
		global $wpdb;

		// Use caching to satisfy PHPCS NoCaching warning
		$cache_key = 'iheu_all_styles';
		$data = wp_cache_get( $cache_key );

		if ( false === $data ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$data = $wpdb->get_results( 'SELECT * FROM ' . esc_sql( $this->parent_table ) . ' ORDER BY id DESC', ARRAY_A );

			// Store in cache
			wp_cache_set( $cache_key, $data );
		}

		$vcdata = [];
		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach ( $data as $value ) {
				if ( isset( $value['id'] ) ) {
					// Visual Composer format: array key = display text, array value = actual value to save
					$label = 'ID: ' . $value['id'] . ( ! empty( $value['name'] ) ? ' - ' . $value['name'] : '' );
					$vcdata[ $label ] = $value['id'];
				}
			}
		}
		
		// Add a default option if no styles exist
		if ( empty( $vcdata ) ) {
			$vcdata = [ esc_html__( 'No styles available', 'image-hover-effects-ultimate' ) => '' ];
		}

		vc_map(
			[
				'name'        => esc_html__( 'Image Hover Ultimate', 'image-hover-effects-ultimate' ),
				'base'        => 'iheu_oxi_VC',
				'category'    => esc_html__( 'Content', 'image-hover-effects-ultimate' ),
				'description' => esc_html__( 'Display Image Hover Effects', 'image-hover-effects-ultimate' ),
				// WPBakery shows a URL here as the element's icon (the old class name matched no icon: a blank placeholder).
				'icon'        => OXI_IMAGE_HOVER_URL . 'image/logo.png',
				'params'      => [
					[
						'type'        => 'dropdown',
						'heading'     => esc_html__( 'Select Image Hover Style', 'image-hover-effects-ultimate' ),
						'param_name'  => 'id',
						'value'       => $vcdata,
						'save_always' => true,
						'description' => esc_html__( 'Select your Image Hover Style ID', 'image-hover-effects-ultimate' ),
						'admin_label' => true,
					],
				],
			]
		);
	}

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->parent_table = $this->wpdb->prefix . 'image_hover_ultimate_style';
        // The element itself is registered from Oxilab_Imagehover::register_vc_element() on vc_before_init.
        add_shortcode( 'iheu_oxi_VC', [ $this, 'iheu_oxi_VC_shortcode' ] );
    }
}
