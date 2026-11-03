<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Includes\Admin;

/**
 * Admin Menu Class
 *
 * @since 2.10.1
 */
class Menu
{

	/**
	 * Database Parent Table
	 *
	 * @since 3.1.0
	 */
	public $parent_table;

	public function __construct()
	{
		global $wpdb;
		$this->parent_table = $wpdb->prefix . 'image_hover_ultimate_style';
		add_action('admin_menu', [$this, 'regiter_admin_menu']);
	}

	public function regiter_admin_menu()
	{
		$first_key = \OXI_IMAGE_HOVER_PLUGINS\Classes\ImageApi::plugin_capability();
		add_menu_page('Image Hover', 'Image Hover', $first_key, 'oxi-image-hover-ultimate', [$this, 'Image_Parent'], OXI_IMAGE_HOVER_URL . 'image/dash-icon.svg');
		add_submenu_page('oxi-image-hover-ultimate', 'Image Hover', 'Image Hover', $first_key, 'oxi-image-hover-ultimate', [$this, 'Image_Parent']);
		add_submenu_page('oxi-image-hover-ultimate', 'Shortcode', 'Shortcode', $first_key, 'oxi-image-hover-shortcode', [$this, 'Image_Shortcode']);
		add_submenu_page('oxi-image-hover-ultimate', 'Getting Started', 'Getting Started', $first_key, 'image-hover-ultimate-getting-started', [$this, 'oxi_image_hover_getting_started']);
		add_submenu_page('oxi-image-hover-ultimate', 'Settings', 'Settings', $first_key, 'oxi-image-hover-ultimate-settings', [$this, 'Image_Settings']);
	}

	public function Image_Parent()
	{
		global $wpdb;
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		$effects = isset($_GET['effects']) && ! empty($_GET['effects']) ? ucfirst(sanitize_text_field(wp_unslash($_GET['effects']))) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		$styleid = isset($_GET['styleid']) && ! empty($_GET['styleid']) ? intval($_GET['styleid']) : '';
		if (! empty($effects) && ! empty($styleid)) :
			$style = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT style_name FROM ' . esc_sql($this->parent_table) . ' WHERE id = %d',
					(int) $styleid
				),
				ARRAY_A
			);
			$name = explode('-', $style['style_name']);
			if ($effects != ucfirst($name[0])) :
				wp_die(esc_html('Invalid URL.'));
			endif;
			$cls = '\OXI_IMAGE_HOVER_PLUGINS\Modules\\' . $effects . '\Admin\\Effects' . $name[1];
			if (class_exists($cls)) :
				new $cls();
			else :
				wp_die(esc_html('Invalid URL.'));
			endif;
		elseif (! empty($effects)) :
			$cls = '\OXI_IMAGE_HOVER_PLUGINS\Modules\\' . $effects . '\\' . $effects . '';
			if (class_exists($cls)) :
				new $cls();
			else :
				wp_die(esc_html('Invalid URL.'));
			endif;
		else :
			new Pages\Admin();
		endif;
	}

	public function Image_Shortcode()
	{
		new Pages\Shortcode();
	}

	public function Image_Settings()
	{
		new Pages\Settings();
	}

	public function oxi_image_hover_getting_started()
	{
		new Pages\GettingStarted();
	}
}
