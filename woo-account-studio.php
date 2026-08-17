<?php
/**
 * Plugin Name:       Woo Account Studio
 * Plugin URI:        https://github.com/Sky-Athena/woo-account-studio
 * Description:       Replaces the default WooCommerce My Account page with a modern, mobile-first customer hub — eight ready-made journeys, a live template studio and a configurable mobile navigation bar.
 * Version:           3.2.0
 * Requires at least: 6.5
 * Tested up to:      7.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * Author:            Sky Athena
 * Author URI:        https://skyathena.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       woo-account-studio
 * Domain Path:       /languages
 * WC requires at least: 8.0
 * WC tested up to:  11.0
 *
 * Woo Account Studio
 * Copyright (C) 2026 Sky Athena Kft.
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * @package WooAccountStudio
 */

defined( 'ABSPATH' ) || exit;

define( 'WCAS_VERSION', '3.2.0' );
define( 'WCAS_FILE', __FILE__ );
define( 'WCAS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCAS_URL', plugin_dir_url( __FILE__ ) );

require_once WCAS_PATH . 'includes/class-wcas-plugin.php';

register_activation_hook( WCAS_FILE, array( 'WCAS_Plugin', 'activate' ) );
register_deactivation_hook( WCAS_FILE, array( 'WCAS_Plugin', 'deactivate' ) );

/**
 * Declare compatibility with WooCommerce High-Performance Order Storage and
 * the cart/checkout blocks. Without this the store shows a compatibility
 * warning on the WooCommerce features screen.
 */
add_action(
	'before_woocommerce_init',
	static function () {
		if ( ! class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			return;
		}

		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WCAS_FILE, true );
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', WCAS_FILE, true );
	}
);

/**
 * Boot the plugin once all plugins are loaded, or explain why it stayed idle.
 */
add_action(
	'plugins_loaded',
	static function () {
		if ( class_exists( 'WooCommerce' ) ) {
			WCAS_Plugin::instance();
			return;
		}

		add_action(
			'admin_notices',
			static function () {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				printf(
					'<div class="notice notice-warning"><p>%s</p></div>',
					esc_html__( 'Woo Account Studio needs WooCommerce to be installed and active. The customer hub stays disabled until then.', 'woo-account-studio' )
				);
			}
		);
	}
);
