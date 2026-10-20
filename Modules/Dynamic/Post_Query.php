<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Dynamic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post_Query {


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

	/**
	 * Database Import Table
	 *
	 * @since 9.3.0
	 */
	public $import_table;

	/**
	 * Database Import Table
	 *
	 * @since 9.3.0
	 */
	public $child_table;

	public function __construct( $function = '', $rawdata = '', $args = '', $optional = '' ) {
		if ( ! empty( $function ) && ! empty( $rawdata ) ) :
			global $wpdb;
			$this->wpdb = $wpdb;
			$this->parent_table = $this->wpdb->prefix . 'image_hover_ultimate_style';
			$this->child_table = $this->wpdb->prefix . 'image_hover_ultimate_list';
			return $this->$function( $rawdata, $args, $optional );
		endif;
	}

	public function __rest_api_post( $style, $args, $optional ) {
		global $wpdb;

		if ( ! is_array( $args ) ) {
			$args = json_decode( stripslashes( $args ), true );
		}

		$args['offset'] = (int) $args['offset'] + ( ( (int) $optional - 1 ) * (int) $args['posts_per_page'] );

		if ( ! is_array( $style ) ) {
			$style = json_decode( stripslashes( $style ), true );
		}

		$rawdata = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . esc_sql( $this->parent_table ) . ' WHERE id = %d',
				$style['display_post_id']
			),
			ARRAY_A
		);

		return $this->post_query( $rawdata, $args, $style );
	}


	public function post_query( $dbdata, $args, $style ) {
		global $wpdb;

		$child = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . esc_sql( $this->child_table ) . ' WHERE styleid = %d',
				$dbdata['id']
			),
			ARRAY_A
		);

		if ( ! is_array( $child ) ) {
			?>
			<p>
				<?php esc_html_e( 'Set Initial Data How to Decorate your Display Post.', 'image-hover-effects-ultimate' ); ?>
				<?php esc_html_e( 'Kindly Check Display Post', 'image-hover-effects-ultimate' ); ?>
				<a href="https://oxilab.dev/docs/image-hover-effects/extensions/display-post-show-wordpress-posts-with-hover-effects/" target="_blank">
					<?php esc_html_e( 'Documentation', 'image-hover-effects-ultimate' ); ?>
				</a>.
			</p>
			<?php
			return;
		}

		$demo     = json_decode( stripslashes( $child['rawdata'] ), true );
		$query    = new \WP_Query( $args );
		$postdata = [];
		$i        = 1;

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$postdata[ $i ] = $child;
				$rawdata        = $demo;
				$query->the_post();

				if ( has_post_thumbnail() ) {
					$rawdata['image_hover_image-image'] = esc_url(
						wp_get_attachment_image_url(
							get_post_thumbnail_id(),
							$style['display_post_thumb_sizes']
						)
					);
					$rawdata['image_hover_image-select'] = 'media-library';
				}

				$rawdata['image_hover_heading']         = get_the_title();
				$rawdata['image_hover_description']     = implode(
					' ',
					array_slice(
						explode(
							' ',
							wp_strip_all_tags(
								strip_shortcodes(
									get_the_excerpt() ? get_the_excerpt() : get_the_content()
								)
							)
						),
						0,
						$style['display_post_excerpt']
					)
				) . ' ...';
				$rawdata['image_hover_button_link-url'] = get_the_permalink();

				$postdata[ $i ]['rawdata'] = wp_json_encode( $rawdata );
				++$i;
			}
		} else {
			// No more posts: only the marker, so Load More removes its button (as Display Post does).
			esc_html_e( 'Image Hover Empty Data', 'image-hover-effects-ultimate' );
			return;
		}

		if ( count( $postdata ) != $args['posts_per_page'] ) {
			esc_html_e( 'Image Hover Empty Data', 'image-hover-effects-ultimate' );
		}

		wp_reset_postdata();

		$StyleName = explode( '-', ucfirst( $dbdata['style_name'] ) );
		$cls       = '\OXI_IMAGE_HOVER_PLUGINS\Modules\\' . $StyleName[0] . '\Render\Effects' . $StyleName[1];
		new $cls( $dbdata, $postdata, 'request' );
	}
}
