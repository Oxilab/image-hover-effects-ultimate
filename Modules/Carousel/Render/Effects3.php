<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Carousel\Render;

if (! defined('ABSPATH')) {
	exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects3 extends Public_Render
{

	public function public_jquery()
	{
		wp_enqueue_script('oxi-image-carousel-swiper.min.js', OXI_IMAGE_HOVER_URL . 'Modules/Carousel/Files/swiper.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
		$this->JSHANDLE = 'oxi-image-carousel-swiper.min.js';
	}

	public function public_css()
	{
		wp_enqueue_style('oxi-image-hover-carousel-swiper.min.css', OXI_IMAGE_HOVER_URL . 'Modules/Carousel/Files/swiper.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
		wp_enqueue_style('oxi-image-hover-style-3', OXI_IMAGE_HOVER_URL . 'Modules/Carousel/Files/style-3.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
	}

	public function render()
	{

		$style = $this->style;

		// Build Swiper config for Elementor editor re-initialization
		$effects         = isset( $style['carousel_effect'] ) ? $style['carousel_effect'] : 'slide';
		$autoplay_delay  = ( isset( $style['carousel_autoplay'] ) && $style['carousel_autoplay'] === 'yes' )
			? (int) $style['carousel_autoplay_speed'] : 99999;
		$speed           = ! empty( $style['carousel_speed'] ) ? (int) $style['carousel_speed'] : 500;
		$pause_on_hover  = ( isset( $style['carousel_pause_on_hover'] ) && $style['carousel_pause_on_hover'] === 'yes' );
		$centeredSlides  = ( $effects === 'coverflow' );
		$infinite        = ( isset( $style['carousel_infinite'] ) && $style['carousel_infinite'] === 'yes' );
		$adaptiveheight  = ( isset( $style['carousel_adaptive_height'] ) && $style['carousel_adaptive_height'] === 'yes' );
		$grab_cursor     = ( isset( $style['carousel_grab_cursor'] ) && $style['carousel_grab_cursor'] === 'yes' );

		$lap    = isset( $style['carousel_item-lap-size'] ) ? $style['carousel_item-lap-size'] : 'auto';
		$tab    = isset( $style['carousel_item-tab-size'] ) ? $style['carousel_item-tab-size'] : 'auto';
		$mobile = isset( $style['carousel_item-mob-size'] ) ? $style['carousel_item-mob-size'] : 'auto';
		if ( $effects === 'cube' ) {
			$lap = 1; $tab = 1; $mobile = 1;
		} elseif ( $effects !== 'coverflow' && $effects !== 'slide' ) {
			$lap = 'auto'; $tab = 'auto'; $mobile = 'auto';
		}

		$swiper_config = [
			'direction'      => 'horizontal',
			'speed'          => $speed,
			'effect'         => $effects,
			'centeredSlides' => $centeredSlides,
			'grabCursor'     => $grab_cursor,
			'autoHeight'     => $adaptiveheight,
			'loop'           => $infinite,
			'cubeEffect'     => [ 'shadow' => false, 'slideShadows' => false, 'shadowOffset' => 0, 'shadowScale' => 0 ],
			'autoplay'       => [ 'delay' => $autoplay_delay ],
			'pagination'     => [ 'el' => '.oxi_carousel_dots_' . $this->oxiid, 'clickable' => true ],
			'navigation'     => [
				'nextEl' => '.oxi_carousel_next_' . $this->oxiid,
				'prevEl' => '.oxi_carousel_prev_' . $this->oxiid,
			],
			'breakpoints'    => [
				960 => [ 'slidesPerView' => $lap ],
				600 => [ 'slidesPerView' => $tab ],
				480 => [ 'slidesPerView' => $mobile ],
			],
			'_pauseOnHover'  => $pause_on_hover,
			'_rtl'           => isset( $style['carousel_direction'] ) ? $style['carousel_direction'] : '',
		];

		$swiper_config_attr = esc_attr( wp_json_encode( $swiper_config ) );
?>
		<div class="oxi-addons-container <?php echo esc_attr($this->WRAPPER); ?> oxi-image-hover-wrapper-
        <?php
		if (array_key_exists('carousel_register_style', $this->style)) :
			echo esc_attr($this->style['carousel_register_style']);
		endif;
		?>
        " id="<?php echo esc_attr($this->WRAPPER); ?>" data-swiper-config="<?php echo $swiper_config_attr; ?>">
			<div class="oxi-addons-row swiper-container oxi-addons-swiper-wrapper">
				<div class="swiper-wrapper">
					<?php
					$this->default_render($this->style, $this->child, 'request');
					?>

				</div>
				<?php
				if ($style['carousel_show_dots'] == 'yes') :
				?>
					<div class="swiper-pagination oxi_carousel_dots oxi_carousel_dots_<?php echo (int) $this->oxiid; ?>"></div>
				<?php
				endif;
				if (array_key_exists('carousel_show_arrows', $style) && $style['carousel_show_arrows'] == 'yes') {
				?>
					<div class="swiper-button-next  oxi_carousel_arrows  oxi_carousel_next oxi_carousel_next_<?php echo (int) $this->oxiid; ?>">
						<?php $this->font_awesome_render($style['carousel_right_arrow']); ?>
					</div>
					<div class="swiper-button-prev oxi_carousel_arrows oxi_carousel_prev oxi_carousel_prev_<?php echo (int) $this->oxiid; ?>">
						<?php $this->font_awesome_render($style['carousel_left_arrow']); ?>
					</div>
				<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	public function public_column_render($col)
	{
		$column = 1;
		if (count(explode('-lg-', $col)) == 2) :
			$column = explode('-lg-', $col)[1];
		elseif (count(explode('-md-', $col)) == 2) :
			$column = explode('-md-', $col)[1];
		elseif (count(explode('-sm-', $col)) == 2) :
			$column = explode('-sm-', $col)[1];
		endif;
		if ($column == 12) :
			return 1;
		elseif ($column == 6) :
			return 2;
		elseif ($column == 4) :
			return 3;
		elseif ($column == 3) :
			return 4;
		elseif ($column == 2) :
			return 6;
		else :
			return 12;
		endif;
	}

	public function inline_public_css()
	{
		$css = '';
		// Fetch and RETURN the button/hover layout's stylesheet
		if (!empty($this->style['carousel_register_style'])) {
			global $wpdb;
			$styledata = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT stylesheet FROM ' . esc_sql($this->parent_table) . ' WHERE id = %d',
					(int) $this->style['carousel_register_style']
				),
				ARRAY_A
			);
			if (is_array($styledata) && !empty($styledata['stylesheet'])) {
				$css = $styledata['stylesheet'];
				// Replace the button's wrapper ID with the carousel's wrapper ID so styles apply
				// The Render skips the button's wrapper div, so valid selectors must use the carousel's wrapper
				if (isset($this->oxiid)) {
					$css = str_replace(
						'oxi-image-hover-wrapper-' . $this->style['carousel_register_style'],
						'oxi-image-hover-wrapper-' . $this->oxiid,
						$css
					);
				}
			}
		}

		return $css;
	}

	public function default_render($style, $child, $admin)
	{
		global $wpdb;
		if (! array_key_exists('carousel_register_style', $style) && $style['carousel_register_style'] < 1) :
		?>
			<p><?php esc_html_e('Kindly Select Image Effects First to Extend Carousel.', 'image-hover-effects-ultimate'); ?></p>
		<?php
			return;
		endif;
		$styledata = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . esc_sql($this->parent_table) . ' WHERE id = %d',
				(int) $style['carousel_register_style']
			),
			ARRAY_A
		);

		if (! is_array($styledata)) :
		?>
			<p> <?php esc_html_e('Style Data not found. Kindly Click the Save button and Check Carousel & Slider', 'image-hover-effects-ultimate'); ?> <a href="https://oxilab.dev/docs/image-hover-effects/extensions/carousel-slider-turn-hover-effects-into-a-sliding-carousel/"><?php esc_html_e('Documentation', 'image-hover-effects-ultimate'); ?></a>.</p>
<?php
			return;
		endif;
		$files = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . esc_sql($this->child_table) . ' WHERE styleid = %d',
				(int) $style['carousel_register_style']
			),
			ARRAY_A
		);
		$StyleName = explode('-', ucfirst($styledata['style_name']));
		$cls = '\OXI_IMAGE_HOVER_PLUGINS\Modules\\' . $StyleName[0] . '\Render\Effects' . $StyleName[1];
		new $cls($styledata, $files, 'request');

		$col = json_decode(stripslashes($styledata['rawdata']), true);

		$lap = $style['carousel_item-lap-size'];
		$tab = $style['carousel_item-tab-size'];
		$mobile = $style['carousel_item-mob-size'];

		// Saved values reach the script only as numbers or as escaped strings (CVE-2026-118056).
		$effects = $style['carousel_effect'];
		$autoplay = ( 'yes' === $style['carousel_autoplay'] ) ? $this->js_number( $style['carousel_autoplay_speed'], 3000 ) : '99999';
		$speed = $this->js_number( ! empty( $style['carousel_speed'] ) ? $style['carousel_speed'] : '', 500 );
		$pause_on_hover = ($style['carousel_pause_on_hover'] == 'yes') ? 'true' : 'false';
		$infinite = ($style['carousel_infinite'] == 'yes') ? 'true' : 'false';
		$adaptiveheight = ($style['carousel_adaptive_height'] == 'yes') ? 'true' : 'false';
		$grab_cursor = ($style['carousel_grab_cursor'] == 'yes') ? 'true' : 'false';
		$rtl = $style['carousel_direction'];

		$centeredSlides = ($effects == 'coverflow') ? 'true' : 'false';
		if ($effects == 'coverflow' || $effects == 'slide') {
			$lap = $lap;
			$tab = $tab;
			$mobile = $mobile;
		} elseif ($effects == 'cube') {
			$lap = 1;
			$tab = 1;
			$mobile = 1;
		} else {
			$lap = 'auto';
			$tab = 'auto';
			$mobile = 'auto';
		}

		$jquery = '(function ($) {
            var oxi_swiper_slider = $("#' . $this->WRAPPER . ' .oxi-addons-row");
            oxi_swiper_slider.find(".oxi-image-hover-style").removeClass().addClass("oxi-image-hover-style swiper-slide");
                if(' . $this->js_string( $rtl ) . ' == "rtl"){
                    $(oxi_swiper_slider).prop("dir", "rtl");
                }
                var oxiSwiperSlider = new Swiper(oxi_swiper_slider, {
                    direction: "horizontal",
                    speed: ' . $speed . ',
                    effect: ' . $this->js_string( $effects ) . ',
                    centeredSlides: ' . $centeredSlides . ',
                    grabCursor: ' . $grab_cursor . ',
                    autoHeight: ' . $adaptiveheight . ',
                    loop: ' . $infinite . ',
                    observer: true,
                    observeParents: true,
                    cubeEffect: {
                        shadow: false,
                        slideShadows: false,
                        shadowOffset: 0,
                        shadowScale: 0,
                    },
                    autoplay: {
                        delay: ' . $autoplay . '
                    },
                    pagination: {
                        el: ".oxi_carousel_dots_' . $this->oxiid . '",
                        clickable: true
                    },
                    navigation: {
                        nextEl: ".oxi_carousel_next_' . $this->oxiid . '",
                        prevEl: ".oxi_carousel_prev_' . $this->oxiid . '"
                    },
                    breakpoints: {
                        960: {
                            slidesPerView: ' . $this->js_string( $lap ) . ',
                        },
                        600 : {
                            slidesPerView: ' . $this->js_string( $tab ) . ',
                        },
                        480: {
                            slidesPerView: ' . $this->js_string( $mobile ) . ',
                        }
                    }
                });
                if (' . $autoplay . ' === 0) {
                    oxiSwiperSlider.autoplay.stop();
                }
                if (' . $pause_on_hover . ' == true) {
                    oxi_swiper_slider.on("mouseenter", function() {
                        oxiSwiperSlider.autoplay.stop();
                    });
                    oxi_swiper_slider.on("mouseleave", function() {
                        oxiSwiperSlider.autoplay.start();
                    });
                };
        })(jQuery);';
		// Skip inline script in Elementor context - handled by elementor-carousel-init.js
		if (!did_action('elementor/preview/enqueue_scripts')) {
			wp_add_inline_script($this->JSHANDLE, $jquery);
		}
	}
}
