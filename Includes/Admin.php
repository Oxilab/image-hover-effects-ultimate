<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Includes;

/**
 * Admin Handler Class
 *
 * @since 2.10.1
 */
class Admin {

	/**
	 * Admin class constructor
	 *
	 * @since 2.10.1
	 */
	public function __construct() {
		new Admin\Menu();
		new Admin\Notice();
		new Admin\Account();
	}
}
