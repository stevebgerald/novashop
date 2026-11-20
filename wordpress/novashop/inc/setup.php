<?php
/**
 * Theme setup, menus, assets.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo', [
        'height'      => 48,
        'width'       => 180,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus([
        'primary'   => __('Menú principal', 'novashop'),
        'topbar'    => __('Barra superior', 'novashop'),
        'footer'    => __('Menú pie', 'novashop'),
        'categories'=> __('Categorías', 'novashop'),
    ]);
});

add_action('wp_enqueue_scripts', function () {
    // Fuentes opcionales (no bloquean el CSS principal).
    wp_enqueue_style(
        'novashop-fonts',
        'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@500;600;700;800&display=swap',
        [],
        null
    );
    wp_enqueue_style('novashop-style', get_stylesheet_uri(), [], NOVASHOP_VERSION);
    wp_enqueue_style('novashop-main', NOVASHOP_URI . '/assets/css/main.css', ['novashop-style'], NOVASHOP_VERSION);
    wp_enqueue_script('novashop-main', NOVASHOP_URI . '/assets/js/main.js', [], NOVASHOP_VERSION, true);
});

add_filter('body_class', function ($classes) {
    if (function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page())) {
        $classes[] = 'novashop-shop';
    }
    return $classes;
});

/**
 * Helper: cart count badge.
 */
function novashop_cart_count() {
    if (!function_exists('WC')) {
        return 0;
    }
    return WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
}
