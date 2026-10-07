<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Includes\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Getting Started page.
 *
 * Also the page Freemius opens right after activation ("first-path"), so it
 * doubles as the plugin's onboarding screen.
 *
 * @author Richard
 */
class GettingStarted
{

	public function __construct()
	{
		$this->Public_Render();
	}

	/**
	 * Number of shortcodes created so far.
	 *
	 * @since 9.12.0
	 *
	 * @return int
	 */
	public function shortcode_count()
	{
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (int) $wpdb->get_var('SELECT COUNT(*) FROM ' . esc_sql($wpdb->prefix . 'image_hover_ultimate_style'));
	}

	public function Public_Render()
	{
		$count      = $this->shortcode_count();
		$is_premium = apply_filters('oxi-image-hover-plugin-version', false) != false;
		$docs       = \OXI_IMAGE_HOVER_PLUGINS\Classes\Docs::links();
		$effects    = admin_url('admin.php?page=oxi-image-hover-ultimate');
		$shortcodes = admin_url('admin.php?page=oxi-image-hover-shortcode');
		$license    = admin_url('admin.php?page=oxi-image-hover-ultimate-settings#license');

		$builders = [
			[ 'icon' => 'block-default', 'name' => __( 'Gutenberg block', 'image-hover-effects-ultimate' ), 'url' => $docs[1]['url'] ],
			[ 'icon' => 'layout', 'name' => __( 'Elementor', 'image-hover-effects-ultimate' ), 'url' => $docs[2]['url'] ],
			[ 'icon' => 'editor-table', 'name' => __( 'WPBakery', 'image-hover-effects-ultimate' ), 'url' => $docs[3]['url'] ],
			[ 'icon' => 'welcome-widgets-menus', 'name' => __( 'WordPress widget', 'image-hover-effects-ultimate' ), 'url' => $docs[4]['url'] ],
		];

		$help = [
			[
				'icon'  => 'book-alt',
				'title' => __( 'Documentation', 'image-hover-effects-ultimate' ),
				'text'  => __( 'Step by step guides for every effect, setting and page builder.', 'image-hover-effects-ultimate' ),
				'link'  => __( 'Read the docs', 'image-hover-effects-ultimate' ),
				'url'   => 'https://oxilab.dev/docs/image-hover-effects/',
			],
			[
				'icon'  => 'video-alt3',
				'title' => __( 'Video tutorials', 'image-hover-effects-ultimate' ),
				'text'  => __( 'Prefer watching? Short videos walk you through the plugin.', 'image-hover-effects-ultimate' ),
				'link'  => __( 'Watch tutorials', 'image-hover-effects-ultimate' ),
				'url'   => 'https://www.youtube.com/@oxilabdev',
			],
			[
				'icon'  => 'sos',
				'title' => __( 'Get support', 'image-hover-effects-ultimate' ),
				'text'  => __( 'Stuck on something? Ask in the support forum and our team will help.', 'image-hover-effects-ultimate' ),
				'link'  => __( 'Open a topic', 'image-hover-effects-ultimate' ),
				'url'   => 'https://wordpress.org/support/plugin/image-hover-effects-ultimate/#new-post',
			],
			[
				'icon'  => 'flag',
				'title' => __( 'Report a bug', 'image-hover-effects-ultimate' ),
				'text'  => __( 'Found something broken? Tell us so we can fix it in the next update.', 'image-hover-effects-ultimate' ),
				'link'  => __( 'Report a bug', 'image-hover-effects-ultimate' ),
				'url'   => 'https://oxilab.dev/support/',
			],
		];

		apply_filters('oxi-image-hover-plugin/admin_menu', true);
?>
	<hr class="wp-header-end">
	<div class="oxi-iheu-settings oxi-iheu-gs">

		<section class="oxi-iheu-gs-hero">
			<div class="oxi-iheu-gs-hero-text">
				<span class="oxi-iheu-gs-eyebrow">
					<img src="<?php echo esc_url(OXI_IMAGE_HOVER_URL . 'image/logo.png'); ?>" alt="" width="20" height="20">
					<?php esc_html_e( 'Getting started', 'image-hover-effects-ultimate' ); ?>
				</span>
				<h1 class="oxi-iheu-gs-title"><?php esc_html_e( 'Welcome to Image Hover Effects', 'image-hover-effects-ultimate' ); ?></h1>
				<p class="oxi-iheu-gs-lead"><?php esc_html_e( 'Turn plain images into eye catching hover effects in a few clicks, then place them anywhere on your site with a shortcode, block or widget.', 'image-hover-effects-ultimate' ); ?></p>
				<div class="oxi-iheu-set-actions">
					<a class="oxi-iheu-set-btn is-primary" href="<?php echo esc_url($effects); ?>">
						<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
						<?php echo esc_html( 0 === $count ? __( 'Create your first effect', 'image-hover-effects-ultimate' ) : __( 'Create a new effect', 'image-hover-effects-ultimate' ) ); ?>
					</a>
					<?php if ( $count > 0 ) : ?>
						<a class="oxi-iheu-set-btn is-secondary" href="<?php echo esc_url($shortcodes); ?>">
							<?php echo esc_html( sprintf( /* translators: %s: number of shortcodes */ _n( 'View your %s shortcode', 'View your %s shortcodes', $count, 'image-hover-effects-ultimate' ), number_format_i18n( $count ) ) ); ?>
						</a>
					<?php else : ?>
						<a class="oxi-iheu-set-btn is-secondary" href="<?php echo esc_url($docs[0]['url']); ?>" target="_blank" rel="noopener">
							<?php esc_html_e( 'Read the guide', 'image-hover-effects-ultimate' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="oxi-iheu-gs-video">
				<iframe src="https://www.youtube.com/embed/SbXhwL2hyVs" title="<?php esc_attr_e( 'Image Hover Effects tutorial', 'image-hover-effects-ultimate' ); ?>" loading="lazy" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
		</section>

		<section class="oxi-iheu-set-card oxi-iheu-gs-steps-card">
			<div class="oxi-iheu-gs-section-head">
				<h2 class="oxi-iheu-gs-h2"><?php esc_html_e( 'Get started in three steps', 'image-hover-effects-ultimate' ); ?></h2>
				<a class="oxi-iheu-set-link" href="<?php echo esc_url($docs[0]['url']); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Full guide', 'image-hover-effects-ultimate' ); ?></a>
			</div>
			<ol class="oxi-iheu-gs-steps">
				<li class="oxi-iheu-gs-step">
					<span class="oxi-iheu-gs-step-num" aria-hidden="true">1</span>
					<h3><?php esc_html_e( 'Choose an effect', 'image-hover-effects-ultimate' ); ?></h3>
					<p><?php esc_html_e( 'Pick an effect type, such as General, Caption or Flipbox, and start from one of its ready made layouts.', 'image-hover-effects-ultimate' ); ?></p>
					<a class="oxi-iheu-gs-step-link" href="<?php echo esc_url($effects); ?>"><?php esc_html_e( 'Browse effects', 'image-hover-effects-ultimate' ); ?><span aria-hidden="true">&rarr;</span></a>
				</li>
				<li class="oxi-iheu-gs-step">
					<span class="oxi-iheu-gs-step-num" aria-hidden="true">2</span>
					<h3><?php esc_html_e( 'Design it', 'image-hover-effects-ultimate' ); ?></h3>
					<p><?php esc_html_e( 'Add your images, titles and links, then fine tune colors, fonts and animations in the live editor. Click Save when it looks right.', 'image-hover-effects-ultimate' ); ?></p>
				</li>
				<li class="oxi-iheu-gs-step">
					<span class="oxi-iheu-gs-step-num" aria-hidden="true">3</span>
					<h3><?php esc_html_e( 'Place it on your site', 'image-hover-effects-ultimate' ); ?></h3>
					<p><?php esc_html_e( 'Copy the shortcode into any page or post, or add it with your page builder.', 'image-hover-effects-ultimate' ); ?></p>
					<a class="oxi-iheu-gs-step-link" href="<?php echo esc_url($shortcodes); ?>"><?php esc_html_e( 'Your shortcodes', 'image-hover-effects-ultimate' ); ?><span aria-hidden="true">&rarr;</span></a>
				</li>
			</ol>
		</section>

		<section class="oxi-iheu-gs-section">
			<div class="oxi-iheu-gs-section-head">
				<h2 class="oxi-iheu-gs-h2"><?php esc_html_e( 'Works with your page builder', 'image-hover-effects-ultimate' ); ?></h2>
			</div>
			<div class="oxi-iheu-gs-builders">
				<?php foreach ( $builders as $builder ) : ?>
					<a class="oxi-iheu-gs-builder" href="<?php echo esc_url($builder['url']); ?>" target="_blank" rel="noopener">
						<span class="oxi-iheu-gs-builder-icon dashicons dashicons-<?php echo esc_attr($builder['icon']); ?>" aria-hidden="true"></span>
						<span class="oxi-iheu-gs-builder-name"><?php echo esc_html($builder['name']); ?></span>
						<span class="oxi-iheu-gs-builder-link"><?php esc_html_e( 'How to use', 'image-hover-effects-ultimate' ); ?><span class="dashicons dashicons-external" aria-hidden="true"></span></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="oxi-iheu-gs-section">
			<div class="oxi-iheu-gs-section-head">
				<h2 class="oxi-iheu-gs-h2"><?php esc_html_e( 'Help and resources', 'image-hover-effects-ultimate' ); ?></h2>
			</div>
			<div class="oxi-iheu-gs-help">
				<?php foreach ( $help as $item ) : ?>
					<a class="oxi-iheu-set-card oxi-iheu-gs-help-card" href="<?php echo esc_url($item['url']); ?>" target="_blank" rel="noopener">
						<span class="oxi-iheu-set-card-icon dashicons dashicons-<?php echo esc_attr($item['icon']); ?>" aria-hidden="true"></span>
						<h3><?php echo esc_html($item['title']); ?></h3>
						<p><?php echo esc_html($item['text']); ?></p>
						<span class="oxi-iheu-gs-help-link"><?php echo esc_html($item['link']); ?><span class="dashicons dashicons-external" aria-hidden="true"></span></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="oxi-iheu-gs-plan<?php echo $is_premium ? ' is-premium' : ''; ?>">
			<?php if ( $is_premium ) : ?>
				<span class="oxi-iheu-gs-plan-icon dashicons dashicons-yes-alt" aria-hidden="true"></span>
				<div class="oxi-iheu-gs-plan-text">
					<strong><?php esc_html_e( 'Premium version is active', 'image-hover-effects-ultimate' ); ?></strong>
					<span><?php esc_html_e( 'Thank you for supporting Image Hover Effects.', 'image-hover-effects-ultimate' ); ?></span>
				</div>
				<a class="oxi-iheu-set-link" href="<?php echo esc_url($license); ?>"><?php esc_html_e( 'Manage license', 'image-hover-effects-ultimate' ); ?></a>
			<?php else : ?>
				<span class="oxi-iheu-gs-plan-icon dashicons dashicons-star-filled" aria-hidden="true"></span>
				<div class="oxi-iheu-gs-plan-text">
					<strong><?php esc_html_e( 'You are using the free version', 'image-hover-effects-ultimate' ); ?></strong>
					<span><?php esc_html_e( 'Already bought Premium? Activate your license key on the Settings page.', 'image-hover-effects-ultimate' ); ?></span>
				</div>
				<div class="oxi-iheu-set-actions">
					<a class="oxi-iheu-set-btn is-secondary" href="<?php echo esc_url($license); ?>"><?php esc_html_e( 'Activate license', 'image-hover-effects-ultimate' ); ?></a>
					<a class="oxi-iheu-set-btn is-primary" href="https://oxilab.dev/image-hover-effects/pricing/" target="_blank" rel="noopener"><?php esc_html_e( 'See Premium plans', 'image-hover-effects-ultimate' ); ?></a>
				</div>
			<?php endif; ?>
		</section>

	</div>
<?php
	}
}
