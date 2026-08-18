# Shoptic for WooCommerce

WordPress plugin that adds a **WooCommerce → Shoptic** page with a "Connect with Shoptic" button. The connection uses WooCommerce's built-in REST API authorization flow (`/wc-auth/v1/authorize`); the plugin itself makes no outbound requests and stores no secrets.

- App: https://app.shoptic.ai
- Plugin listing copy: `readme.txt` (wp.org format)
- License: GPLv2 or later

## Development

Zip for manual install:

```sh
zip -r shoptic-woocommerce.zip shoptic-woocommerce.php includes assets languages readme.txt LICENSE
```
