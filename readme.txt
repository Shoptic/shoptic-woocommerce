=== Shoptic for WooCommerce ===
Contributors: shoptic
Tags: woocommerce, ai, product images, product photos, product descriptions
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect your WooCommerce store to Shoptic and generate professional product photos, videos and listings with AI.

== Description ==

Shoptic turns your product photos into professional, on-brand images, videos and optimized titles and descriptions using AI. No photographers, no studio, no editing.

This plugin adds a **Shoptic** page under WooCommerce with a single "Connect with Shoptic" button. The connection itself uses WooCommerce's own REST API authorization screen: you review and approve read/write access to your products from your own WordPress, and you can revoke it at any time from WooCommerce → Settings → Advanced → REST API.

**What you can do once connected**

* Browse your WooCommerce catalog inside Shoptic.
* Generate lifestyle, studio and ad-style images for any product and publish them back to the product gallery.
* Generate optimized titles, descriptions and tags (and SEO title/description if you use Yoast SEO or Rank Math) and apply them to the product.
* Keep everything in sync: Shoptic is notified when products change.

**Privacy**

The plugin does not send any data on its own and does not store any credentials. Once you approve the connection, Shoptic (https://shoptic.ai) accesses your products through the WooCommerce REST API. See https://shoptic.ai/privacy.

== Installation ==

1. Install and activate the plugin.
2. Go to WooCommerce → Shoptic and click "Connect with Shoptic".
3. Sign in (or create a free account) and approve the connection in your WordPress when asked.

== Frequently Asked Questions ==

= Do I need a Shoptic account? =
Yes. You can create a free account during the connection flow.

= What permissions does Shoptic get? =
Read/write access to your WooCommerce REST API (products). You approve it explicitly and can revoke it from WooCommerce → Settings → Advanced → REST API.

= My site is behind a firewall / security plugin =
Make sure the WooCommerce REST API (`/wp-json/wc/v3/`) is reachable and that your site uses HTTPS. Permalinks must not be set to "Plain".

== Changelog ==

= 1.0.0 =
* Initial release: connect page, connection status, welcome notice.
