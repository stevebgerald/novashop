<?php
/**
 * Template: Checkout
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>

<div class="ns-checkout-wrap">
    <h1 class="ns-page-title">Checkout</h1>
    <p class="ns-checkout-lead">Completá tus datos para confirmar la compra de forma segura.</p>

    <form name="checkout" method="post" class="checkout woocommerce-checkout ns-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

        <?php if ($checkout->get_checkout_fields()) : ?>
            <div class="ns-checkout__grid">
                <div class="ns-checkout__fields">
                    <?php do_action('woocommerce_checkout_before_customer_details'); ?>
                    <div id="customer_details">
                        <div class="col-1">
                            <?php do_action('woocommerce_checkout_billing'); ?>
                        </div>
                        <div class="col-2">
                            <?php do_action('woocommerce_checkout_shipping'); ?>
                        </div>
                    </div>
                    <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                </div>

                <aside class="ns-checkout__order">
                    <h3 id="order_review_heading"><?php esc_html_e('Your order', 'woocommerce'); ?></h3>
                    <?php do_action('woocommerce_checkout_before_order_review'); ?>
                    <div id="order_review" class="woocommerce-checkout-review-order">
                        <?php do_action('woocommerce_checkout_order_review'); ?>
                    </div>
                    <?php do_action('woocommerce_checkout_after_order_review'); ?>
                </aside>
            </div>
        <?php endif; ?>

    </form>
</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
