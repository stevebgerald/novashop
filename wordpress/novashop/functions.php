<?php
/**
 * NovaShop theme functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('NOVASHOP_VERSION', '1.1.0');
define('NOVASHOP_DIR', get_template_directory());
define('NOVASHOP_URI', get_template_directory_uri());

require_once NOVASHOP_DIR . '/inc/setup.php';
require_once NOVASHOP_DIR . '/inc/woocommerce.php';
require_once NOVASHOP_DIR . '/inc/contact.php';
require_once NOVASHOP_DIR . '/inc/demo-products.php';
