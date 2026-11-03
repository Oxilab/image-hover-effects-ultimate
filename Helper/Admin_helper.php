<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Helper;

if (! defined('ABSPATH')) {
	exit;
}

trait Admin_helper
{

	public function admin_url_convert($agr)
	{
		return admin_url(strpos($agr, 'edit') !== false ? $agr : 'admin.php?page=' . $agr);
	}

	/**
	 * Image Hover Admin menu
	 *
	 * @since 9.3.0
	 */
	public function oxilab_admin_menu($agr)
	{
		$response = [
			'Image Hover' => [
				'name' => 'Image Hover',
				'homepage' => 'oxi-image-hover-ultimate',
			],
			'Shortcode' => [
				'name' => 'Shortcode',
				'homepage' => 'oxi-image-hover-shortcode',
			]
		];

		$bgimage = OXI_IMAGE_HOVER_URL . 'image/sm-logo.svg';
?>


		<div class="oxi-addons-wrapper">
			<div class="oxilab-new-admin-menu">
				<div class="oxi-site-logo">
					<a href="<?php echo esc_url($this->admin_url_convert('oxi-image-hover-ultimate')); ?>" class="header-logo" style=" background-image: url(<?php echo esc_url($bgimage); ?>);">
					</a>
				</div>
				<nav class="oxilab-sa-admin-nav">
					<ul class="oxilab-sa-admin-menu">
						<?php
						// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
						$GETPage = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
						// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
						$effects = isset($_GET['effects']) && ! empty($_GET['effects']) ? sanitize_text_field(wp_unslash($_GET['effects'])) : '';
						if ($effects != '' && $GETPage == 'oxi-image-hover-ultimate') :
							$url = $this->admin_url_convert('oxi-image-hover-ultimate') . '&effects=' . $effects;
						?>

							<li class="active">
								<a href="<?php echo esc_url($url); ?>">
									<?php
									if ($effects == 'display') :
										echo 'Display Post';
									else :
										echo esc_html($this->name_converter($effects)) . ' Effects';
									endif;
									?>
								</a>
							</li>
						<?php
						endif;
						foreach ($response as $key => $value) {
							$active = (($GETPage == $value['homepage'] && $effects == '') ? 'active ' : '');
						?>
							<li class="<?php echo esc_attr($active); ?>">
								<a href="<?php echo esc_url($this->admin_url_convert($value['homepage'])); ?>"><?php echo esc_html($this->name_converter($value['name'])); ?></a>
							</li>
						<?php
						}
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in Docs.
						echo \OXI_IMAGE_HOVER_PLUGINS\Classes\Docs::nav_item();
						?>
					</ul>
					<ul class="oxilab-sa-admin-menu2">

						<?php
						if (apply_filters('oxi-image-hover-plugin-version', false) == false) :
						?>
							<li class="fazil-class">
								<a target="_blank" href="https://oxilab.dev/image-hover-effects/pricing/">Upgrade
								</a>
							</li>
						<?php
						endif;
						?>
						<li class="saadmin-doc">
							<a target="_blank" href="https://demos.oxilab.dev/imagehover/demos/">Demo</a>
						</li>
						<li class="saadmin-doc">
							<a target="_black" href="https://oxilab.dev/docs/image-hover-effects/">Docs</a>
						</li>
						<li class="saadmin-doc">
							<a target="_black" href="https://wordpress.org/support/plugin/image-hover-effects-ultimate/">Support
							</a>
						</li>
						<li class="saadmin-set">
							<a href="<?php echo esc_url(admin_url('admin.php?page=oxi-image-hover-ultimate-settings')); ?>">
								<span class="dashicons dashicons-admin-generic"></span>
							</a>
						</li>
					</ul>
				</nav>
			</div>
		</div>
	<?php
	}
	public function Public_loader()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->parent_table = $this->wpdb->prefix . 'image_hover_ultimate_style';
		$this->child_table = $this->wpdb->prefix . 'image_hover_ultimate_list';
		$this->import_table = $this->wpdb->prefix . 'oxi_div_import';
	}

	/**
	 * Plugin fixed
	 *
	 * @since 9.3.0
	 */
	public function fixed_data($agr)
	{
		return hex2bin($agr);
	}

	/**
	 * Plugin fixed debugging data
	 *
	 * @since 9.3.0
	 */
	public function fixed_debug_data($str)
	{
		return bin2hex($str);
	}

	/**
	 * Admin Notice Check
	 *
	 * @since 9.3.0
	 */
	public function admin_notice_status()
	{
		$data = get_option('oxi_image_hover_nobug');
		return $data;
	}

	public function User_Reviews()
	{

		if ( ! current_user_can( \OXI_IMAGE_HOVER_PLUGINS\Classes\ImageApi::plugin_capability() ) ) :
			return;
		endif;
		$this->admin_recommended();
		// Only ever one of these two at a time, so the dashboard is never stacked
		// with plugin notices.
		if (! $this->admin_notice()) :
			$this->admin_upgrade_notice();
		endif;
	}

	/**
	 * Admin Notice Check
	 *
	 * @since 9.3.0
	 */
	public function admin_recommended_status()
	{

		$data = get_option('oxi_image_hover_recommended');
		return $data;
	}
	public function SupportAndComments($agr)
	{
		if (get_option('oxi_image_support_massage') === 'no') {
			return;
		}
	?>
<?php
	}

	public function admin_recommended()
	{

		if (! empty($this->admin_recommended_status())) :
			return;
		endif;

		if (strtotime('-1 days') < $this->installation_date()) :
			return;
		endif;

		new \OXI_IMAGE_HOVER_PLUGINS\Classes\Support_Recommended();
	}

	/**
	 * Plugin check Current Tabs
	 *
	 * @since 9.3.0
	 */
	public function check_current_version($agr)
	{
		$vs = get_option($this->fixed_data('696d6167655f686f7665725f756c74696d6174655f6c6963656e73655f737461747573'));

		if ($vs == $this->fixed_data('76616c6964') || oxilab_iheu_v()->can_use_premium_code()) {
			return true;
		} else {
			return false;
		}
	}

	public function admin_notice()
	{
		if (! empty($this->admin_notice_status())) :
			return false;
		endif;
		if (strtotime('-7 days') < $this->installation_date()) :
			return false;
		endif;
		new \OXI_IMAGE_HOVER_PLUGINS\Classes\Support_Reviews();
		return true;
	}

	/**
	 * Pro upgrade notice gate.
	 *
	 * Free users only, after 10 days of use, until dismissed for good. The pro
	 * check calls check_current_version() directly rather than through the
	 * 'oxi-image-hover-plugin-version' filter, because Admin_Filters() has not
	 * registered that filter yet at the point User_Reviews() runs.
	 *
	 * @since 9.11.8
	 */
	public function admin_upgrade_notice()
	{
		if ($this->has_pro_access()) :
			return false;
		endif;
		if (! empty(get_option('oxi_image_hover_upgrade_nobug'))) :
			return false;
		endif;
		if (strtotime('-10 days') > $this->upgrade_notice_date()) :
			new \OXI_IMAGE_HOVER_PLUGINS\Classes\Support_Upgrade();
			return true;
		endif;
		return false;
	}

	/**
	 * Whether this install has a valid licence or premium code available.
	 *
	 * Wrapped defensively: this runs earlier than the existing pro checks, and a
	 * missing Freemius bootstrap must never be able to fatal the admin.
	 *
	 * @since 9.11.8
	 */
	public function has_pro_access()
	{
		$has_pro = false;
		try {
			$license = get_option($this->fixed_data('696d6167655f686f7665725f756c74696d6174655f6c6963656e73655f737461747573'));
			if ($license === $this->fixed_data('76616c6964')) :
				$has_pro = true;
			elseif (function_exists('oxilab_iheu_v') && oxilab_iheu_v()->can_use_premium_code()) :
				$has_pro = true;
			endif;
		} catch (\Throwable $e) {
			// Treat an unavailable licence layer as "unknown", and stay quiet
			// rather than risk showing an upgrade prompt to a paying customer.
			$has_pro = true;
		}

		/**
		 * Filters whether this install counts as having Pro access.
		 *
		 * Only governs whether the upgrade notice is shown, it unlocks nothing.
		 * Useful for QA, where a licenced dev site needs to preview the free
		 * user experience.
		 *
		 * @since 9.11.8
		 *
		 * @param bool $has_pro
		 */
		return (bool) apply_filters('oxi_image_hover_has_pro_access', $has_pro);
	}

	/**
	 * Clock for the upgrade notice.
	 *
	 * Seeded from the real installation date so existing sites are credited for
	 * the time they have already used the plugin, then kept separate from it, so
	 * snoozing the review notice cannot move this one.
	 *
	 * @since 9.11.8
	 */
	public function upgrade_notice_date()
	{
		$data = get_option('oxi_image_hover_upgrade_date');
		if (empty($data)) :
			$data = $this->installation_date();
			update_option('oxi_image_hover_upgrade_date', $data);
		endif;
		return $data;
	}

	/**
	 * Admin Install date Check
	 *
	 * @since 9.3.0
	 */
	public function installation_date()
	{
		$data = get_option('oxi_image_hover_activation_date');
		if (empty($data)) :
			$data = strtotime('now');
			update_option('oxi_image_hover_activation_date', $data);
		endif;
		return $data;
	}

	public function Admin_Filters()
	{
		add_filter($this->fixed_data('6f78692d696d6167652d686f7665722d737570706f72742d616e642d636f6d6d656e7473'), [$this, $this->fixed_data('537570706f7274416e64436f6d6d656e7473')]);
		add_filter($this->fixed_data('6f78692d696d6167652d686f7665722d706c7567696e2d76657273696f6e'), [$this, $this->fixed_data('636865636b5f63757272656e745f76657273696f6e')]);
		add_filter($this->fixed_data('6f78692d696d6167652d686f7665722d706c7567696e2f61646d696e5f6d656e75'), [$this, $this->fixed_data('6f78696c61625f61646d696e5f6d656e75')]);
	}
}
