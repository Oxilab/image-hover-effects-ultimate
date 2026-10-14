<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * "How to use?" documentation menu.
 *
 * One source of links, rendered into both admin headers: the plugin's own menu
 * bar (Helper/Admin_helper.php) and the full-screen effect editor header
 * (Page/Admin_Render.php).
 *
 * @since 9.11.8
 */
class Docs {

	/**
	 * Documentation entries, in menu order. The last one is the "browse all"
	 * footer link; the others are guides with a short line and an icon.
	 *
	 * @return array<int, array{label: string, url: string, desc?: string, icon?: string}>
	 */
	public static function links() {
		return [
			[
				'label' => __( 'Creating your first hover effect', 'image-hover-effects-ultimate' ),
				'desc'  => __( 'Pick a design, add your images and publish', 'image-hover-effects-ultimate' ),
				'icon'  => 'plugin',
				'url'   => 'https://oxilab.dev/docs/image-hover-effects/ihe-getting-started/creating-your-first-hover-effect/',
			],
			[
				'label' => __( 'Using the native Gutenberg block', 'image-hover-effects-ultimate' ),
				'desc'  => __( 'Add the Image Hover Effects block to any page', 'image-hover-effects-ultimate' ),
				'icon'  => 'block',
				'url'   => 'https://oxilab.dev/docs/image-hover-effects/page-builder-integration/using-the-native-gutenberg-block/',
			],
			[
				'label' => __( 'Using it with Elementor', 'image-hover-effects-ultimate' ),
				'desc'  => __( 'Add the Image Hover widget in Elementor', 'image-hover-effects-ultimate' ),
				'icon'  => 'elementor',
				'url'   => 'https://oxilab.dev/docs/image-hover-effects/page-builder-integration/how-to-use-image-hover-effects-with-elementor/',
			],
			[
				'label' => __( 'Using it with WPBakery', 'image-hover-effects-ultimate' ),
				'desc'  => __( 'Add hover effects to your WPBakery layouts', 'image-hover-effects-ultimate' ),
				'icon'  => 'wpbakery',
				'url'   => 'https://oxilab.dev/docs/image-hover-effects/page-builder-integration/how-to-use-with-wpbakery-visual-composer/',
			],
			[
				'label' => __( 'Using it as a WordPress widget', 'image-hover-effects-ultimate' ),
				'desc'  => __( 'Show a hover effect in a sidebar or footer', 'image-hover-effects-ultimate' ),
				'icon'  => 'wordpress',
				'url'   => 'https://oxilab.dev/docs/image-hover-effects/page-builder-integration/using-as-a-wordpress-widget-in-gutenberg/',
			],
			[
				'label' => __( 'Browse all documentation', 'image-hover-effects-ultimate' ),
				'url'   => 'https://oxilab.dev/docs/image-hover-effects/',
			],
		];
	}

	/**
	 * Icon for a guide row (inline SVG, or a core dashicon).
	 *
	 * @param string $name Icon key from links().
	 * @return string
	 */
	private static function icon( $name ) {
		switch ( $name ) {
			case 'plugin': // The Image Hover mark, from image/sm-logo.svg.
				return '<svg viewBox="0 0 43.68 43.68" width="24" height="24" aria-hidden="true"><circle fill="#ee326e" cx="21.84" cy="21.84" r="21.84"/><g fill="#fff"><path d="M30.56,32.22h-12.82c-2.86,0-5.19-2.33-5.19-5.19v-12.82c0-2.86,2.33-5.19,5.19-5.19h12.82c2.86,0,5.19,2.33,5.19,5.19v12.82c0,2.86-2.33,5.19-5.19,5.19ZM17.74,10.42c-2.08,0-3.78,1.7-3.78,3.78v12.82c0,2.08,1.7,3.78,3.78,3.78h12.82c2.08,0,3.78-1.7,3.78-3.78v-12.82c0-2.08-1.7-3.78-3.78-3.78h-12.82Z"/><path d="M16.77,35.33c-2.13,0-4.13-1.33-4.87-3.46l-4.07-11.65c-.45-1.29-.37-2.69.23-3.94.6-1.24,1.64-2.18,2.94-2.63l2.07-.72.46,1.32-2.07.72c-.94.33-1.7,1.01-2.14,1.92-.44.91-.5,1.93-.17,2.87l4.07,11.65c.68,1.96,2.83,2.99,4.78,2.31l8.21-2.86.46,1.32-8.21,2.86c-.56.2-1.13.29-1.69.29Z"/><path d="M20.01,18.11h-2.68c-.83,0-1.5-.67-1.5-1.5v-2.88c0-.83.67-1.5,1.5-1.5h2.68c.83,0,1.5.67,1.5,1.5v2.88c0,.83-.67,1.5-1.5,1.5ZM17.33,13.28c-.24,0-.44.2-.44.44v2.88c0,.24.2.44.44.44h2.68c.24,0,.44-.2.44-.44v-2.88c0-.24-.2-.44-.44-.44h-2.68Z"/><path d="M30.43,28.94c-.16,0-.31-.07-.42-.2l-2.39-3.08-1.42,1.42c-.15.14-.36.19-.55.12-.19-.07-.33-.25-.35-.45l-.67-7.6c-.02-.19.07-.38.23-.49.16-.11.37-.12.54-.03l6.38,3.35c.18.1.29.29.28.5-.01.21-.14.39-.34.46l-1.5.58,2.36,3.27c.17.23.12.56-.11.73l-1.72,1.31c-.1.07-.21.11-.32.11ZM27.67,24.33s.02,0,.03,0c.15,0,.29.08.38.2l2.44,3.14.9-.68-2.46-3.41c-.1-.13-.12-.3-.08-.46s.16-.28.32-.34l1.05-.41-4.49-2.35.49,5.51,1.04-1.04c.1-.1.23-.15.37-.15Z"/><path d="M13.25,26.32c-.2,0-.41-.09-.55-.26-.25-.3-.2-.75.1-.99l5.17-4.24c.24-.2.58-.21.84-.04l4.76,3.17c.32.22.41.65.2.98-.21.32-.65.41-.98.2l-4.32-2.88-4.77,3.91c-.13.11-.29.16-.45.16Z"/></g></svg>';
			case 'block': // A block, as in the block inserter.
				return '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2" fill="#344054"/><rect x="13" y="13" width="8" height="8" rx="2" fill="#344054"/><rect x="3" y="13" width="8" height="8" rx="2" fill="#98a2b3"/><rect x="14.2" y="2.6" width="6.4" height="6.4" rx="1.6" transform="rotate(45 17.4 5.8)" fill="#ee326e"/></svg>';
			case 'elementor':
				return '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><circle cx="12" cy="12" r="12" fill="#92003b"/><rect x="7" y="6.5" width="2.4" height="11" fill="#fff"/><rect x="11.2" y="6.5" width="6" height="2.4" fill="#fff"/><rect x="11.2" y="10.8" width="6" height="2.4" fill="#fff"/><rect x="11.2" y="15.1" width="6" height="2.4" fill="#fff"/></svg>';
			case 'wpbakery':
				return '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><path fill="#0473aa" d="M7.2 19.5h9.9a4.4 4.4 0 0 0 .9-8.7 6 6 0 0 0-11.5-1.4A5.1 5.1 0 0 0 7.2 19.5z"/></svg>';
			case 'wordpress':
				return '<span class="dashicons dashicons-wordpress" aria-hidden="true"></span>';
		}

		return '';
	}

	/**
	 * The dropdown panel shared by both headers.
	 *
	 * Keeps the .oxi-howto-menu class the headers already open on hover and
	 * focus; everything inside is styled by assets/backend/css/howto.css.
	 *
	 * @return string
	 */
	private static function panel() {
		$links  = self::links();
		$browse = array_pop( $links );

		$out  = '<div class="oxi-howto-menu oxi-iheu-howto">';
		$out .= '<div class="oxi-iheu-howto-head">'
			. '<span class="oxi-iheu-howto-eyebrow">' . esc_html__( 'Guides', 'image-hover-effects-ultimate' ) . '</span>'
			. '<span class="oxi-iheu-howto-sub">' . esc_html__( 'Step by step help for every way to use Image Hover', 'image-hover-effects-ultimate' ) . '</span>'
			. '</div>';
		$out .= '<div class="oxi-iheu-howto-list">';
		foreach ( $links as $link ) {
			$out .= '<a class="oxi-iheu-howto-link" href="' . esc_url( $link['url'] ) . '" target="_blank" rel="noopener noreferrer">'
				. '<span class="oxi-iheu-howto-icon">' . self::icon( $link['icon'] ) . '</span>'
				. '<span class="oxi-iheu-howto-text">'
				. '<span class="oxi-iheu-howto-title">' . esc_html( $link['label'] ) . '</span>'
				. '<span class="oxi-iheu-howto-desc">' . esc_html( $link['desc'] ) . '</span>'
				. '</span>'
				. '</a>';
		}
		$out .= '</div>';
		$out .= '<a class="oxi-iheu-howto-foot" href="' . esc_url( $browse['url'] ) . '" target="_blank" rel="noopener noreferrer">'
			. '<span class="dashicons dashicons-book" aria-hidden="true"></span>'
			. '<span class="oxi-iheu-howto-foot-label">' . esc_html( $browse['label'] ) . '</span>'
			. '<span class="dashicons dashicons-external" aria-hidden="true"></span>'
			. '</a>';
		$out .= '</div>';

		return $out;
	}

	/**
	 * Menu item for the plugin's main admin menu bar (a list item in
	 * ul.oxilab-sa-admin-menu2).
	 *
	 * @return string
	 */
	public static function nav_item() {
		return '<li class="saadmin-doc oxi-howto-item">'
			. '<a href="' . esc_url( self::links()[0]['url'] ) . '" target="_blank" rel="noopener noreferrer" class="oxi-howto-toggle">'
			. '<span class="dashicons dashicons-editor-help" aria-hidden="true"></span>'
			. esc_html__( 'How to use?', 'image-hover-effects-ultimate' )
			. '</a>'
			. self::panel()
			. '</li>';
	}

	/**
	 * Menu item for the full-screen effect editor header.
	 *
	 * @return string
	 */
	public static function editor_item() {
		return '<div class="oxi-howto-item oxi-howto-item--editor">'
			. '<a href="' . esc_url( self::links()[0]['url'] ) . '" target="_blank" rel="noopener noreferrer" class="oxi-btn-howto oxi-howto-toggle">'
			. '<i class="fa fa-question-circle" aria-hidden="true"></i> '
			. esc_html__( 'How to use?', 'image-hover-effects-ultimate' )
			. '</a>'
			. self::panel()
			. '</div>';
	}
}
