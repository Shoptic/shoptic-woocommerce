<?php
/**
 * Admin page: WooCommerce → Shoptic.
 *
 * Shows whether the store is connected (there is a WooCommerce REST API key
 * created by the Shoptic authorization flow) and a button that sends the
 * merchant to the Shoptic app with the store URL pre-filled. Nothing else.
 *
 * @package Shoptic_WooCommerce
 */

defined( 'ABSPATH' ) || exit;

class Shoptic_WC_Admin {

	const MENU_SLUG = 'shoptic';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 60 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'admin_notices', array( __CLASS__, 'maybe_welcome_notice' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_dismiss_notice' ) );
	}

	public static function register_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Shoptic', 'shoptic-woocommerce' ),
			__( 'Shoptic', 'shoptic-woocommerce' ),
			'manage_woocommerce',
			self::MENU_SLUG,
			array( __CLASS__, 'render_page' )
		);
	}

	public static function enqueue( $hook ) {
		if ( 'woocommerce_page_' . self::MENU_SLUG !== $hook ) {
			return;
		}
		wp_enqueue_style( 'shoptic-wc-admin', SHOPTIC_WC_URL . 'assets/admin.css', array(), SHOPTIC_WC_VERSION );
	}

	/**
	 * A REST API key whose description starts with "Shoptic" means the merchant
	 * completed the /wc-auth flow (WooCommerce names the key "<app_name> - API read/write").
	 *
	 * @return bool
	 */
	public static function is_connected() {
		global $wpdb;
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE %s",
				$wpdb->esc_like( 'Shoptic' ) . '%'
			)
		);
		return (int) $count > 0;
	}

	/**
	 * Deep link into the Shoptic app: opens the WooCommerce connect dialog with
	 * this store's URL pre-filled. Requires the app to support ?connect=woocommerce&store=.
	 *
	 * @return string
	 */
	public static function connect_url() {
		return add_query_arg(
			array(
				'connect' => 'woocommerce',
				'store'   => rawurlencode( home_url( '/' ) ),
				'utm_source' => 'wp-plugin',
			),
			shoptic_wc_app_url() . '/stores'
		);
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'shoptic-woocommerce' ) );
		}
		$connected = self::is_connected();
		$app_url   = shoptic_wc_app_url();
		?>
		<div class="wrap shoptic-wc-wrap">
			<h1><?php esc_html_e( 'Shoptic', 'shoptic-woocommerce' ); ?></h1>
			<div class="shoptic-wc-card">
				<h2><?php esc_html_e( 'AI product photos, videos and listings for your WooCommerce store', 'shoptic-woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'Shoptic connects to your catalog and generates professional product images, videos and optimized titles and descriptions with AI. Approve the connection from this WordPress and start creating in minutes.', 'shoptic-woocommerce' ); ?></p>

				<?php if ( $connected ) : ?>
					<p class="shoptic-wc-status shoptic-wc-status--ok">
						<span class="dashicons dashicons-yes-alt"></span>
						<?php esc_html_e( 'This store is connected to Shoptic.', 'shoptic-woocommerce' ); ?>
					</p>
					<p>
						<a class="button button-primary button-hero" href="<?php echo esc_url( $app_url ); ?>" target="_blank" rel="noopener">
							<?php esc_html_e( 'Open Shoptic', 'shoptic-woocommerce' ); ?>
						</a>
						<a class="button button-secondary button-hero" href="<?php echo esc_url( self::connect_url() ); ?>" target="_blank" rel="noopener">
							<?php esc_html_e( 'Reconnect', 'shoptic-woocommerce' ); ?>
						</a>
					</p>
				<?php else : ?>
					<p class="shoptic-wc-status">
						<span class="dashicons dashicons-marker"></span>
						<?php esc_html_e( 'This store is not connected yet.', 'shoptic-woocommerce' ); ?>
					</p>
					<p>
						<a class="button button-primary button-hero" href="<?php echo esc_url( self::connect_url() ); ?>" target="_blank" rel="noopener">
							<?php esc_html_e( 'Connect with Shoptic', 'shoptic-woocommerce' ); ?>
						</a>
					</p>
					<ol class="shoptic-wc-steps">
						<li><?php esc_html_e( 'Click "Connect with Shoptic". You will be taken to Shoptic (create a free account if you do not have one).', 'shoptic-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Shoptic sends you back to this WordPress to approve read/write access to your products.', 'shoptic-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Done: your catalog appears in Shoptic and everything you generate can be published back to your products.', 'shoptic-woocommerce' ); ?></li>
					</ol>
				<?php endif; ?>

				<h3><?php esc_html_e( 'Requirements', 'shoptic-woocommerce' ); ?></h3>
				<ul class="shoptic-wc-reqs">
					<li><?php esc_html_e( 'Your site must use HTTPS.', 'shoptic-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'Permalinks must not be set to "Plain" (Settings → Permalinks).', 'shoptic-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'The WooCommerce REST API must be reachable (security plugins and firewalls must allow it).', 'shoptic-woocommerce' ); ?></li>
				</ul>
				<p class="shoptic-wc-privacy">
					<?php esc_html_e( 'This plugin does not send any data by itself. Access is granted through the WooCommerce REST API authorization screen and can be revoked at any time from WooCommerce → Settings → Advanced → REST API.', 'shoptic-woocommerce' ); ?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * One-time notice after activation pointing to the connect page.
	 */
	public static function maybe_welcome_notice() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		if ( get_option( 'shoptic_wc_notice_dismissed' ) || self::is_connected() ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && 'woocommerce_page_' . self::MENU_SLUG === $screen->id ) {
			return;
		}
		$dismiss = wp_nonce_url( add_query_arg( 'shoptic_wc_dismiss', '1' ), 'shoptic_wc_dismiss' );
		?>
		<div class="notice notice-info is-dismissible">
			<p>
				<strong><?php esc_html_e( 'Shoptic for WooCommerce', 'shoptic-woocommerce' ); ?></strong>:
				<?php esc_html_e( 'connect your store to start generating AI product content.', 'shoptic-woocommerce' ); ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::MENU_SLUG ) ); ?>"><?php esc_html_e( 'Connect now', 'shoptic-woocommerce' ); ?></a>
				&nbsp;|&nbsp;
				<a href="<?php echo esc_url( $dismiss ); ?>"><?php esc_html_e( 'Dismiss', 'shoptic-woocommerce' ); ?></a>
			</p>
		</div>
		<?php
	}

	public static function handle_dismiss_notice() {
		if ( ! isset( $_GET['shoptic_wc_dismiss'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_woocommerce' ) || ! wp_verify_nonce( isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '', 'shoptic_wc_dismiss' ) ) {
			return;
		}
		update_option( 'shoptic_wc_notice_dismissed', 1 );
		wp_safe_redirect( remove_query_arg( array( 'shoptic_wc_dismiss', '_wpnonce' ) ) );
		exit;
	}
}
