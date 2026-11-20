<?php
/**
 * WooCommerce hooks & layout tweaks.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', function () {
    // Remove default Woo wrappers; theme handles layout.
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
});

add_filter('woocommerce_enqueue_styles', '__return_empty_array');

add_filter('loop_shop_per_page', fn () => 12);
add_filter('loop_shop_columns', fn () => 3);

add_filter('woocommerce_product_add_to_cart_text', function ($text) {
    return __('Agregar al carrito', 'novashop');
});

add_filter('woocommerce_product_single_add_to_cart_text', function () {
    return __('Agregar al carrito', 'novashop');
});

/**
 * Discount badge on loop.
 */
function novashop_sale_badge() {
    global $product;
    if (!$product || !$product->is_on_sale()) {
        return;
    }
    if ($product->is_type('variable')) {
        echo '<span class="ns-badge">Oferta</span>';
        return;
    }
    $regular = (float) $product->get_regular_price();
    $sale    = (float) $product->get_sale_price();
    if ($regular > 0 && $sale > 0) {
        $pct = round((($regular - $sale) / $regular) * 100);
        echo '<span class="ns-badge">-' . esc_html($pct) . '%</span>';
    }
}

/**
 * Star rating HTML for loop.
 */
function novashop_loop_rating() {
    global $product;
    if (!$product || !wc_review_ratings_enabled()) {
        return;
    }
    echo '<div class="ns-rating">' . wc_get_rating_html($product->get_average_rating()) . '</div>';
}

/**
 * Product card used on home & shop.
 */
function novashop_product_card($product_id = null) {
    $product = $product_id ? wc_get_product($product_id) : wc_get_product(get_the_ID());
    if (!$product) {
        return;
    }
    $link = get_permalink($product->get_id());
    ?>
    <article class="ns-card">
        <a class="ns-card__media" href="<?php echo esc_url($link); ?>">
            <?php
            echo $product->get_image('woocommerce_thumbnail', ['class' => 'ns-card__img']);
            novashop_sale_badge();
            ?>
        </a>
        <div class="ns-card__body">
            <?php novashop_loop_rating(); ?>
            <h3 class="ns-card__title">
                <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($product->get_name()); ?></a>
            </h3>
            <div class="ns-card__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
            <div class="ns-card__actions">
                <?php woocommerce_template_loop_add_to_cart(); ?>
            </div>
        </div>
    </article>
    <?php
}

/**
 * AJAX cart fragment for header badge.
 */
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    ob_start();
    ?>
    <span class="ns-cart-count" data-count="<?php echo esc_attr(novashop_cart_count()); ?>">
        <?php echo esc_html(novashop_cart_count()); ?>
    </span>
    <?php
    $fragments['span.ns-cart-count'] = ob_get_clean();
    return $fragments;
});
