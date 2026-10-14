<?php

/**
 * Preview Frame Template
 * Renders ONLY the image hover effect block for iframe preview
 *
 * @package image-hover-effects-ultimate
 * @since 10.0.0
 */

namespace OXI_IMAGE_HOVER_PLUGINS\Page;

if (! defined('ABSPATH')) {
	exit;
}

/**
 * PreviewFrame Class
 * Minimal template for iframe preview rendering
 */
class PreviewFrame
{

	/**
	 * Layout ID
	 *
	 * @var int
	 */
	private $layout_id;

	/**
	 * Layout data from database
	 *
	 * @var array
	 */
	private $dbdata;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->layout_id = isset($_GET['styleid']) ? intval($_GET['styleid']) : 0;

		if (! $this->layout_id) {
			wp_die(esc_html__('Invalid layout ID', 'image-hover-effects-ultimate'));
		}

		$this->load_layout_data();

		// Isolate preview environment
		add_action('wp_enqueue_scripts', [$this, 'assets_isolation'], 9999);
		add_action('wp_print_styles', [$this, 'assets_isolation'], 9999);

		$this->render();
	}

	/**
	 * De queue unwanted styles and scripts
	 */
	public function assets_isolation()
	{
		global $wp_scripts, $wp_styles;

		// Allowed Handles (Core and This Plugin)
		$allowed = [
			// WordPress Core
			'jquery',
			'jquery-core',
			'jquery-migrate',
			'wp-util',
			'underscore',
			'backbone',
			'wp-embed',
			'wp-api',
			'wp-polyfill',
			'regenerator-runtime',
			'common',
			'admin-bar',
			'buttons',

			// Image Hover Plugin (Our own assets)
			'image-hover-addons',
			'image-hover-front',
			'oxi-image-hover-front',
			'oxi-image-hover',
			'oxi-image-hover-iframe-forwarder',
			'oxi-image-hover-overlay-scrollbar',

			// OverlayScrollbars Library
			'overlayscrollbars',

			// Font Awesome (often needed)
			'font-awesome',
			'fontawesome',
		];

		// Dequeue Scripts
		if (isset($wp_scripts->queue)) {
			foreach ($wp_scripts->queue as $handle) {
				if (!in_array($handle, $allowed) && !strpos($handle, 'oxi') !== false && !strpos($handle, 'image-hover') !== false) {
					wp_dequeue_script($handle);
					wp_deregister_script($handle);
				}
			}
		}

		// Dequeue Styles
		if (isset($wp_styles->queue)) {
			foreach ($wp_styles->queue as $handle) {
				if (!in_array($handle, $allowed) && !strpos($handle, 'oxi') !== false && !strpos($handle, 'image-hover') !== false) {
					wp_dequeue_style($handle);
					wp_deregister_style($handle);
				}
			}
		}
	}

	/**
	 * Load layout data from database
	 */
	private function load_layout_data()
	{
		global $wpdb;

		$this->dbdata = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . $wpdb->prefix . 'image_hover_ultimate_style WHERE id = %d',
				$this->layout_id
			),
			ARRAY_A
		);

		if (! $this->dbdata) {
			wp_die(esc_html__('Layout not found', 'image-hover-effects-ultimate'));
		}
	}

	/**
	 * Render the preview frame
	 */
	private function render()
	{
		$style_name = $this->dbdata['style_name'];

		// Load font families if any
		$font_family = ! empty($this->dbdata['font_family']) ? json_decode($this->dbdata['font_family'], true) : [];

?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>

		<head>
			<meta charset="<?php bloginfo('charset'); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="robots" content="noindex, nofollow">
			<title><?php esc_html_e('Preview', 'image-hover-effects-ultimate'); ?></title>


			<?php
			// Enqueue admin styles for button styling
			wp_enqueue_style('oxi-image-hover-bootstrap', OXI_IMAGE_HOVER_URL . 'assets/backend/css/bootstrap.min.css', array(), OXI_IMAGE_HOVER_PLUGIN_VERSION);
			wp_enqueue_style('oxi-image-hover-admin', OXI_IMAGE_HOVER_URL . 'assets/backend/css/admin.css', array(), OXI_IMAGE_HOVER_PLUGIN_VERSION);
			wp_enqueue_style('oxi-image-hover-single-editor', OXI_IMAGE_HOVER_URL . 'assets/backend/css/single_editor_page.css', array(), OXI_IMAGE_HOVER_PLUGIN_VERSION);
			wp_enqueue_style('oxi-image-hover-frontend-style', OXI_IMAGE_HOVER_URL . 'assets/frontend/css/style.css', array(), OXI_IMAGE_HOVER_PLUGIN_VERSION);
			// Look of the Edit / Clone / Del toolbar on each preview item.
			wp_enqueue_style('oxi-image-hover-preview-frame', OXI_IMAGE_HOVER_URL . 'assets/backend/css/preview-frame.css', array('oxi-image-hover-admin', 'oxi-image-hover-single-editor'), filemtime(OXI_IMAGE_HOVER_PATH . 'assets/backend/css/preview-frame.css'));

			// Enqueue OverlayScrollbars library for preview iframe
			wp_enqueue_style('overlayscrollbars', OXI_IMAGE_HOVER_URL . 'assets/backend/css/overlayscrollbars.min.css', array(), '2.4.6');
			wp_enqueue_script('overlayscrollbars', OXI_IMAGE_HOVER_URL . 'assets/backend/js/overlayscrollbars.browser.es6.min.js', array(), '2.4.6', false);

			wp_enqueue_style('image-hover-addons');
			wp_enqueue_script('image-hover-addons');
			wp_enqueue_script('oxi-image-hover-iframe-forwarder', OXI_IMAGE_HOVER_URL . 'assets/backend/js/iframe-button-forwarders.js', array('jquery'), filemtime(OXI_IMAGE_HOVER_PATH . 'assets/backend/js/iframe-button-forwarders.js'));
			wp_enqueue_script('oxi-image-hover-overlay-scrollbar', OXI_IMAGE_HOVER_URL . 'assets/backend/js/overlay-scrollbar.js', array('jquery', 'overlayscrollbars'), filemtime(OXI_IMAGE_HOVER_PATH . 'assets/backend/js/overlay-scrollbar.js'));

			// Load essential WordPress styles
			wp_head();


			// Load saved stylesheet
			if (! empty($this->dbdata['stylesheet'])) {
				echo '<style id="ih-saved-styles">' . wp_strip_all_tags($this->dbdata['stylesheet']) . '</style>';
			}


			// If this is a carousel or display module, also load the nested button/hover layout stylesheet
			$style_parts = explode('-', $style_name);
			$module_type = strtolower($style_parts[0]);

			if ($module_type === 'carousel' || $module_type === 'display') {
				$rawdata = ! empty($this->dbdata['rawdata']) ? json_decode($this->dbdata['rawdata'], true) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				$nested_style_id = 0;
				if ($module_type === 'carousel' && ! empty($rawdata['carousel_register_style'])) {
					$nested_style_id = $rawdata['carousel_register_style'];
				} elseif ($module_type === 'display' && ! empty($rawdata['display_post_style'])) {
					$nested_style_id = $rawdata['display_post_style'];
				}

				if ($nested_style_id) {
					global $wpdb;
					$parent_table = $wpdb->prefix . 'image_hover_ultimate_style';
					$button_data = $wpdb->get_row(
						$wpdb->prepare(
							'SELECT stylesheet FROM ' . esc_sql($parent_table) . ' WHERE id = %d',
							(int) $nested_style_id
						),
						ARRAY_A
					);
					if (is_array($button_data) && ! empty($button_data['stylesheet'])) {
						$button_css = html_entity_decode(str_replace('<br>', '', str_replace('&nbsp;', ' ', $button_data['stylesheet'])));
						// Replace the button's wrapper ID with the current wrapper ID so styles apply
						$button_css = str_replace(
							'oxi-image-hover-wrapper-' . $nested_style_id,
							'oxi-image-hover-wrapper-' . $this->layout_id,
							$button_css
						);
						echo '<style id="ih-nested-styles">' . $button_css . '</style>';
					}
				}
			}

			// Load font families
			if (! empty($font_family) && function_exists('font_familly_validation')) {
				font_familly_validation($font_family);
			}
			?>
		</head>

		<body class="oxi-preview-frame">
			<?php
			// Structure must match editor selectors exactly:
			// #oxi-addons-preview-data .oxi-image-hover-wrapper-[ID] .oxi-addons-row ...

			echo '<div id="oxi-addons-preview-data">'; // The ID expected by selectors
			echo '<div class="oxi-image-hover-wrapper-' . $this->layout_id . '">';
			echo '<div class="oxi-addons-row">';

			// Determine which render class to use (before rendering, to check if carousel)
			$style_parts = explode('-', $style_name);
			$is_carousel_or_filter = in_array(strtolower($style_parts[0]), ['carousel', 'filter']);

			// Inner content wrapper for isolation ONLY for non-carousel layouts
			// Carousel layouts need full width and have their own container structure
			if (!$is_carousel_or_filter) {
				echo '<div class="oxi-isolated-content">';
			}

			// Render with admin mode
			global $wpdb;
			$child_table = $wpdb->prefix . 'image_hover_ultimate_list';
			$child = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . esc_sql($child_table) . ' WHERE styleid = %d ORDER BY id ASC',
					$this->layout_id
				),
				ARRAY_A
			);

			$render_class = '\\OXI_IMAGE_HOVER_PLUGINS\\Modules\\' . ucfirst($style_parts[0]) . '\\Render\\Effects' . $style_parts[1];

			// Render with admin mode
			if (class_exists($render_class)) {
				new $render_class($this->dbdata, $child, 'admin');
			} else {
				// Fallback to shortcode if class not found
				echo do_shortcode('[iheu_ultimate_oxi id="' . $this->layout_id . '"]');
			}

			// Close isolation wrapper only if it was opened
			if (!$is_carousel_or_filter) {
				echo '</div>'; // End .oxi-isolated-content
			}
			echo '</div>'; // End .oxi-addons-row
			echo '</div>'; // End .oxi-image-hover-wrapper-[ID]

			// Load footer scripts (Directly print scripts to avoid unwanted content from wp_footer hook)
			wp_print_footer_scripts();
			?>
			<!-- Dynamic styles injection point (Moved to footer to override saved styles) -->
			<style id="ih-dynamic-styles"></style>
		</body>

		</html>
<?php
		exit;
	}
}
