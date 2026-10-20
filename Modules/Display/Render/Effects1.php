<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Display\Render;

if (! defined('ABSPATH')) {
	exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;
use OXI_IMAGE_HOVER_PLUGINS\Modules\Display\Files\Style_1_Post_Query as Post_Query;

class Effects1 extends Public_Render
{


	public function public_jquery()
	{
		wp_enqueue_script('oxi_image_style_1_loader', OXI_IMAGE_HOVER_URL . 'Modules/Display/Files/style-1-loader.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
		$this->JSHANDLE = 'oxi_image_style-1-loader';
		wp_localize_script(
			'oxi_image_style_1_loader',
			'oxi_image_style_1_loader',
			[
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('image_hover_ultimate'),
			]
		);
	}

	public function public_css()
	{
		wp_enqueue_style('oxi-image-hover-display-style-1', OXI_IMAGE_HOVER_URL . 'Modules/Display/Files/style-1.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
	}

	public function inline_public_css()
	{
		$css = '';
		if (! empty($this->style['display_post_style'])) {
			global $wpdb;
			$styledata = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT stylesheet FROM ' . esc_sql($this->parent_table) . ' WHERE id = %d',
					(int) $this->style['display_post_style']
				),
				ARRAY_A
			);
			if (is_array($styledata) && ! empty($styledata['stylesheet'])) {
				$css = $styledata['stylesheet'];
				if (isset($this->oxiid)) {
					$css = str_replace(
						'oxi-image-hover-wrapper-' . $this->style['display_post_style'],
						'oxi-image-hover-wrapper-' . $this->oxiid,
						$css
					);
				}
			}
		}

		return $css;
	}

	public function render()
	{
?>


		<div class="oxi-addons-container <?php echo esc_attr($this->WRAPPER); ?> oxi-image-hover-wrapper-
            <?php
			if (array_key_exists('display_post_style', $this->style)) :
				echo esc_attr($this->style['display_post_style']);
			endif;
			?>
            " id="<?php echo esc_attr($this->WRAPPER); ?>">
			<div class="oxi-addons-row">
				<?php
				$this->default_render($this->style, $this->child, $this->admin);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * The posts query and card settings of a Display Post style, built from its saved settings only. Used by the page
	 * render and by the public Load More request (ImageApi::load_more_action()), which sends nothing but the style id
	 * and the page number.
	 *
	 * @since 9.12.2
	 * @param array $style Saved settings of the Display Post style.
	 * @return array{args: array, settings: array}
	 */
	public static function load_more_query( $style ) {
		$args = [
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'post_type'           => $style['display_post_post_type'],
			'orderby'             => $style['display_post_orderby'],
			'order'               => $style['display_post_ordertype'],
			'posts_per_page'      => $style['display_post_per_page'],
			'offset'              => $style['display_post_offset'],
			'tax_query'           => [], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- the category and tag filters of the style.
		];
		if ( ! empty( $style['display_post_author'] ) ) {
			$args['author__in'] = $style['display_post_author'];
		}

		$type = $style['display_post_post_type'];

		if ( ! empty( $style[ $type . '_exclude' ] ) ) {
			$args['post__not_in'] = $style[ $type . '_exclude' ]; // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- the style's Exclude Post setting.
		}
		if ( ! empty( $style[ $type . '_include' ] ) ) {
			$args['post__in'] = $style[ $type . '_include' ];
		}
		if ( 'page' !== $type ) {
			if ( ! empty( $style[ $type . '_category' ] ) ) {
				$args['tax_query'][] = [
					'taxonomy' => 'post' === $type ? 'category' : $type . '_category',
					'field'    => 'term_id',
					'terms'    => $style[ $type . '_category' ],
				];
			}
			if ( ! empty( $style[ $type . '_tag' ] ) ) {
				$args['tax_query'][] = [
					'taxonomy' => $type . '_tag',
					'field'    => 'term_id',
					'terms'    => $style[ $type . '_tag' ],
				];
			}
			if ( ! empty( $args['tax_query'] ) ) {
				$args['tax_query']['relation'] = 'OR';
			}
		}
		$settings = [
			'display_post_style'       => $style['display_post_style'],
			'display_post_thumb_sizes' => $style['display_post_thumb_sizes'],
			'display_post_excerpt'     => $style['display_post_excerpt'],
		];

		return [
			'args'     => $args,
			'settings' => $settings,
		];
	}

	public function default_render( $style, $child, $admin ) {
		if ( ! array_key_exists( 'display_post_style', $style ) ) :
			?>
			<p>Kindly Select Image Effects First to Extend Post.</p>
			<?php
			return;
		endif;
		$query = self::load_more_query( $style );

		new Post_Query( 'post_query', 'nai', $query['args'], $query['settings'] );

		// Load More sends only this style's id and the next page number; the query is built again on the server.
		if ( 'yes' === $style['display_post_load_more'] ) {
			if ( 'button' === $style['display_post_load_more_type'] ) :
			?>
				<div class="oxi-image-hover-load-more-button-wrap oxi-bt-col-sm-12">
					<button class="oxi-image-load-more-button"
						id="oxi-image-load-more-button<?php echo (int) $this->oxiid; ?>"
						data-styleid="<?php echo (int) $this->oxiid; ?>" data-page="1">
						<div class="oxi-image-hover-loader button__loader"></div>
						<span><?php $this->text_render($style['display_post_load_button_text']); ?></span>
					</button>
				</div>
			<?php
			else :
			?>
				<div class="oxi-image-hover-load-more-infinite"
					id="oxi-image-hover-load-more-infinite<?php echo (int) $this->oxiid; ?>"
					data-styleid="<?php echo (int) $this->oxiid; ?>" data-page="1">
				</div>
<?php
			endif;
		}
	}
}
