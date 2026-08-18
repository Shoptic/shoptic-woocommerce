<?php
/**
 * Plugin Name:       Shoptic for WooCommerce
 * Plugin URI:        https://shoptic.ai/woocommerce
 * Description:       Connect your WooCommerce store to Shoptic and generate professional product photos, videos and listings with AI. No photographers, no studio, no editing.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * WC requires at least: 6.0
 * WC tested up to:   9.9
 * Author:            Shoptic
 * Author URI:        https://shoptic.ai
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       shoptic-woocommerce
 * Domain Path:       /languages
 *
 * This plugin does not make any outbound requests and does not store secrets.
 * It only adds a "Shoptic" page under WooCommerce with a "Connect" button that
 * sends the merchant to the Shoptic app; the actual authorization happens
 * through WooCommerce's own REST API authorization flow (/wc-auth/v1/authorize),
 * where the merchant reviews and approves the access from their own wp-admin.
 */

defined( 'ABSPATH' ) || exit;

define( 'SHOPTIC_WC_VERSION', '1.0.0' );
define( 'SHOPTIC_WC_FILE', __FILE__ );
define( 'SHOPTIC_WC_DIR', plugin_dir_path( __FILE__ ) );
define( 'SHOPTIC_WC_URL', plugin_dir_url( __FILE__ ) );

/**
 * Base URL of the Shoptic app.
 *
 * @return string
 */
function shoptic_wc_app_url() {
	$url = apply_filters( 'shoptic_app_url', 'https://app.shoptic.ai' );
	return untrailingslashit( esc_url_raw( $url ) );
}

/**
 * Declare compatibility with WooCommerce HPOS (we do not touch orders at all).
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

add_action(
	'plugins_loaded',
	function () {
		load_plugin_textdomain( 'shoptic-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action(
				'admin_notices',
				function () {
					if ( ! current_user_can( 'activate_plugins' ) ) {
						return;
					}
					echo '<div class="notice notice-error"><p>' .
						esc_html__( 'Shoptic for WooCommerce requires WooCommerce to be installed and active.', 'shoptic-woocommerce' ) .
						'</p></div>';
				}
			);
			return;
		}

		require_once SHOPTIC_WC_DIR . 'includes/class-shoptic-wc-admin.php';
		Shoptic_WC_Admin::init();
	}
);

/**
 * "Connect" link next to Activate/Deactivate on the plugins list.
 */
add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	function ( $links ) {
		$url = admin_url( 'admin.php?page=shoptic' );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Connect', 'shoptic-woocommerce' ) . '</a>' );
		return $links;
	}
);
