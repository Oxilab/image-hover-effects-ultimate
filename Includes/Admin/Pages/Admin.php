<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Includes\Admin\Pages;

if (! defined('ABSPATH')) {
	exit;
}

class Admin
{

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

	use \OXI_IMAGE_HOVER_PLUGINS\Helper\Public_Helper;
	use \OXI_IMAGE_HOVER_PLUGINS\Helper\CSS_JS_Loader;

	/**
	 * Constructor of Image Hover Home Page
	 *
	 * @since 9.3.0
	 */
	public function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->parent_table = $wpdb->prefix . 'image_hover_ultimate_style';
		$this->child_table = $wpdb->prefix . 'image_hover_ultimate_list';
		$this->import_table = $wpdb->prefix . 'oxi_div_import';

		$this->CSSJS_load();
		$this->Render();
	}

	/**
	 * Admin Notice JS file loader
	 * @return void
	 */
	public function admin_rest_api()
	{
		wp_enqueue_script('oxi-image-hover-shortcode', OXI_IMAGE_HOVER_URL . 'assets/backend/js/home.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
	}

	public function name_converter($data)
	{
		$data = str_replace('_', ' ', $data);
		$data = str_replace('-', ' ', $data);
		$data = str_replace('+', ' ', $data);
		echo esc_html(ucwords($data));
	}

	public function Render()
	{
?>
		<hr class="wp-header-end">
		<div class="oxi-iheu-settings oxi-iheu-home">
			<?php
			$this->Elements_Render();
			?>
		</div>
	<?php
	}

	/**
	 * Each effect type's name, as shown on its card.
	 *
	 * @since 9.12.1
	 * @return array
	 */
	public function effect_texts()
	{
		return [
			'button'     => [__('Button Effects', 'image-hover-effects-ultimate')],
			'general'    => [__('General Effects', 'image-hover-effects-ultimate')],
			'square'     => [__('Square Effects', 'image-hover-effects-ultimate')],
			'caption'    => [__('Caption Effects', 'image-hover-effects-ultimate')],
			'flipbox'    => [__('Flipbox Effects', 'image-hover-effects-ultimate')],
			'magnifier'  => [__('Image Magnifier', 'image-hover-effects-ultimate')],
			'comparison' => [__('Image Comparison', 'image-hover-effects-ultimate')],
			'lightbox'   => [__('Image Lightbox', 'image-hover-effects-ultimate')],
			'display'    => [__('Display Post', 'image-hover-effects-ultimate')],
			'carousel'   => [__('Carousel Slider', 'image-hover-effects-ultimate')],
			'filter'     => [__('Filter & Sorting', 'image-hover-effects-ultimate')],
		];
	}

	/**
	 * Number of ready made layouts of an effect type (its Layouts folders).
	 *
	 * @since 9.12.1
	 * @param string $effect Effect key, for example "button".
	 * @return int
	 */
	public function layout_count($effect)
	{
		$dirs = glob(OXI_IMAGE_HOVER_PATH . 'Modules/' . ucfirst($effect) . '/Layouts/*', GLOB_ONLYDIR);
		return is_array($dirs) ? count($dirs) : 0;
	}

	public function Elements_Render()
	{
		$is_pro = apply_filters('oxi-image-hover-plugin-version', false) == false ? 'Premium' : '';
		$Elements = [
			'Image Effects' => [
				'button' => [
					'name' => 'button-effects',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/button-effects.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/button-effects/',
					'doc' => 'https://oxilab.dev/docs/image-hover-effects/effect-types/button-overlay-hover-effects/',
					'status' => 'Popular',
					'items' => 0
				],
				'general' => [
					'name' => 'general-effects',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/general-effects.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/general-effects/',
					'doc' => 'https://oxilab.dev/docs/image-hover-effects/effect-types/general-hover-effects/',
					'status' => '',
					'items' => 0
				],
				'square' => [
					'name' => 'square-effects',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/square-effects.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/square-effects/',
					'doc' => 'https://oxilab.dev/docs/image-hover-effects/effect-types/square-image-magnifier-filter-gallery-carousel-effects/',
					'status' => 'New',
					'items' => 0
				],
				'caption' => [
					'name' => 'caption-effects',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/caption-effects.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/caption-effects/',
					'doc'    => 'https://oxilab.dev/docs/image-hover-effects/effect-types/caption-hover-effects/',
					'status' => '',
					'items' => 0
				],
				'flipbox' => [
					'name' => 'flipbox-effects',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/flipbox-effects.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/flipbox-effects/',
					'doc' => 'https://oxilab.dev/docs/image-hover-effects/effect-types/flip-box-hover-effects/',
					'status' => 'Popular',
					'items' => 0
				],
				'magnifier' => [
					'name' => 'image-magnifier',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/image-magnifier.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/image-magnifier/',
					'doc' => 'https://oxilab.dev/docs/image-hover-effects/effect-types/square-image-magnifier-filter-gallery-carousel-effects/',
					'status' => 'Updated',
					'items' => 0
				],
				'comparison' => [
					'name' => 'image-comparison',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/image-comparison.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/image-comparison/',
					'doc' => 'https://oxilab.dev/docs/image-hover-effects/effect-types/before-after-image-comparison-slider/',
					'status' => '',
					'items' => 0
				],
				'lightbox' => [
					'name' => 'image-lightbox',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/image-lightbox.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/image-lightbox/',
					'doc' => 'https://oxilab.dev/docs/image-hover-effects/effect-types/lightbox-gallery-effects/',
					'status' => 'Updated',
					'items' => 0
				],
			],
			'Extension' => [
				'display' => [
					'name' => 'display-post',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/display-post.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/display-post/',
					'doc' => 'https://oxilab.dev/docs/image-hover-effects/extensions/display-post-show-wordpress-posts-with-hover-effects/',
					'status' => $is_pro,
					'items' => ''
				],
				'carousel' => [
					'name' => 'carousel-slider',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/carousel-slider.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/carousel-slider/',
					'doc' => 'https://oxilab.dev/docs/image-hover-effects/extensions/carousel-slider-turn-hover-effects-into-a-sliding-carousel/',
					'status' => $is_pro,
					'items' => ''
				],
				'filter' => [
					'name' => 'filter-&-sorting',
					'version' => 1.0,
					'icon' => OXI_IMAGE_HOVER_URL . 'image/icons/filter-sorting.svg',
					'demos' => 'https://demos.oxilab.dev/imagehover/elements-extension/filter-sorting/',
					'doc' => 'https://oxilab.dev/docs/image-hover-effects/extensions/filter-sorting-create-a-filterable-image-gallery/',
					'status' => $is_pro,
					'items' => ''
				],
			],
		];
	?>
		<?php
		$texts  = $this->effect_texts();
		$is_free = apply_filters('oxi-image-hover-plugin-version', false) == false;
		$total  = (int) $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->parent_table}"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from $wpdb->prefix.
		// Saved styles per effect type (style_name is "button-3", "general-1", ...).
		$saved = [];
		foreach ((array) $this->wpdb->get_results("SELECT SUBSTRING_INDEX(style_name, '-', 1) AS effect, COUNT(*) AS total FROM {$this->parent_table} GROUP BY effect", ARRAY_A) as $row) { // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from $wpdb->prefix.
			$saved[strtolower($row['effect'])] = (int) $row['total'];
		}
		$groups = [
			'Image Effects' => [__('Image Effects', 'image-hover-effects-ultimate')],
			'Extension'     => [__('Extensions', 'image-hover-effects-ultimate')],
		];
		?>
		<header class="oxi-iheu-set-hero">
			<img class="oxi-iheu-set-hero-logo" src="<?php echo esc_url(OXI_IMAGE_HOVER_URL . 'image/logo.png'); ?>" alt="" width="52" height="52">
			<div class="oxi-iheu-set-hero-text">
				<h1 class="oxi-iheu-set-title"><?php esc_html_e('Image Hover Effects', 'image-hover-effects-ultimate'); ?></h1>
				<p class="oxi-iheu-set-subtitle"><?php esc_html_e('Choose an effect type, pick a design, then add your images in the editor.', 'image-hover-effects-ultimate'); ?></p>
			</div>
			<a class="oxi-iheu-set-btn is-secondary" href="<?php echo esc_url(admin_url('admin.php?page=oxi-image-hover-shortcode')); ?>">
				<span class="dashicons dashicons-shortcode" aria-hidden="true"></span><?php esc_html_e('Your shortcodes', 'image-hover-effects-ultimate'); ?>
				<span class="oxi-iheu-home-total"><?php echo esc_html(number_format_i18n($total)); ?></span>
			</a>
		</header>

		<?php
		foreach ($Elements as $key => $elements) {
			$group = isset($groups[$key]) ? $groups[$key] : [$key, ''];
			$locked = $is_free && 'Extension' === $key;
		?>
			<section class="oxi-iheu-home-group">
				<div class="oxi-iheu-home-group-head">
					<h2><?php echo esc_html($group[0]); ?> <span class="oxi-iheu-home-group-count"><?php echo (int) count($elements); ?></span></h2>
					<?php if (! empty($group[1])) : ?><p><?php echo esc_html($group[1]); ?></p><?php endif; ?>
				</div>
				<div class="oxi-iheu-home-grid">
					<?php
					foreach ($elements as $k => $value) {
						$oxilink = admin_url('admin.php?page=oxi-image-hover-ultimate&effects=' . $k);
						$title   = isset($texts[$k]) ? $texts[$k][0] : ucwords(str_replace(['-', '_'], ' ', $value['name']));
						$layouts = $this->layout_count($k);
						$badge   = $value['status'];
						$sub     = $locked ? 'premium' : '';
					?>
						<div class="oxi-iheu-home-card" id="<?php echo esc_attr($value['name']); ?>">
							<a class="oxi-iheu-home-card-main addons-pre-check" href="<?php echo esc_url($oxilink); ?>" sub-type="<?php echo esc_attr($sub); ?>">
								<span class="oxi-iheu-home-icon"><img src="<?php echo esc_url($value['icon']); ?>" alt=""></span>
								<span class="oxi-iheu-home-card-text">
									<span class="oxi-iheu-home-card-title"><?php echo esc_html($title); ?></span>
									<span class="oxi-iheu-home-card-meta">
										<?php
										$meta = [];
										if ($layouts) {
											$meta[] = sprintf(/* translators: %s: number of layouts */ _n('%s layout', '%s layouts', $layouts, 'image-hover-effects-ultimate'), number_format_i18n($layouts));
										}
										if (! empty($saved[$k])) {
											$meta[] = sprintf(/* translators: %s: number of saved styles */ _n('%s saved', '%s saved', $saved[$k], 'image-hover-effects-ultimate'), number_format_i18n($saved[$k]));
										}
										echo esc_html(implode(' · ', $meta));
										?>
									</span>
								</span>
								<?php if ($badge) : ?>
									<span class="oxi-iheu-home-badge is-<?php echo esc_attr(strtolower($badge)); ?>"><?php echo 'Premium' === $badge ? '<span class="dashicons dashicons-lock" aria-hidden="true"></span>' : ''; ?><?php echo esc_html('Premium' === $badge ? __('Pro', 'image-hover-effects-ultimate') : $badge); ?></span>
								<?php endif; ?>
							</a>
							<div class="oxi-iheu-home-actions">
								<a class="oxi-iheu-home-action addons-pre-check" href="<?php echo esc_url($oxilink); ?>" sub-type="<?php echo esc_attr($sub); ?>"><span class="dashicons dashicons-layout" aria-hidden="true"></span><?php esc_html_e('Templates', 'image-hover-effects-ultimate'); ?></a>
								<a class="oxi-iheu-home-action" href="<?php echo esc_url($value['doc']); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-media-document" aria-hidden="true"></span><?php esc_html_e('Docs', 'image-hover-effects-ultimate'); ?></a>
								<a class="oxi-iheu-home-action" href="<?php echo esc_url($value['demos']); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-visibility" aria-hidden="true"></span><?php esc_html_e('Demo', 'image-hover-effects-ultimate'); ?></a>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			</section>
		<?php
		}
	}
	public function Admin_header()
	{
	?>
		<div class="oxi-addons-wrapper">
			<div class="oxi-addons-import-layouts">
				<h1>Image Hover › Shortcode</h1>
				<p>Collect Image Hover Shortcode, Edit, Delect, Clone or Export it. </p>
			</div>
		</div>
	<?php
	}

	public function CSSJS_load()
	{
		$this->admin_js();
		$this->admin_home();
		$this->admin_rest_api();
		apply_filters('oxi-image-hover-plugin/admin_menu', true);
	}
	public function font_awesome_render($data)
	{
		$fadata = get_option('oxi_addons_font_awesome');
		if ($fadata != 'no') :
			wp_enqueue_style('font-awsome.min', OXI_IMAGE_HOVER_URL . 'assets/frontend/css/font-awsome.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
		endif;
	?>
		<i class="<?php echo esc_attr($data); ?> oxi-icons"></i>
<?php
	}
}
