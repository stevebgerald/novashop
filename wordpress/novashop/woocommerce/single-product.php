<?php
/**
 * Template: Ficha de producto
 */

defined('ABSPATH') || exit;

get_header('shop');
?>

<div class="ns-container ns-product">
    <?php
    while (have_posts()) {
        the_post();
        global $product;
        ?>
        <nav class="ns-breadcrumbs" aria-label="Breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a>
            <span>/</span>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Tienda</a>
            <span>/</span>
            <span><?php the_title(); ?></span>
        </nav>

        <div class="ns-product__layout">
            <div class="ns-product__gallery">
                <?php
                if ($product->is_on_sale()) {
                    novashop_sale_badge();
                }
                woocommerce_show_product_images();
                ?>
            </div>
            <div class="ns-product__summary">
                <h1><?php the_title(); ?></h1>
                <?php woocommerce_template_single_rating(); ?>
                <div class="ns-product__price"><?php woocommerce_template_single_price(); ?></div>
                <div class="ns-product__excerpt"><?php woocommerce_template_single_excerpt(); ?></div>
                <div class="ns-product__cart"><?php woocommerce_template_single_add_to_cart(); ?></div>
                <ul class="ns-product__meta-list">
                    <li>Envío rápido a todo el país</li>
                    <li>Pago seguro y protegido</li>
                    <li>Garantía oficial</li>
                </ul>
            </div>
        </div>

        <section class="ns-product__tabs ns-section">
            <h2>Descripción</h2>
            <div class="ns-page__body">
                <?php the_content(); ?>
            </div>
        </section>

        <?php
        $related = wc_get_related_products($product->get_id(), 4);
        if ($related) :
            ?>
            <section class="ns-section">
                <div class="ns-section__head">
                    <h2>También te puede interesar</h2>
                </div>
                <div class="ns-grid ns-grid--4">
                    <?php foreach ($related as $related_id) {
                        novashop_product_card($related_id);
                    } ?>
                </div>
            </section>
        <?php endif; ?>
        <?php
    }
    ?>
</div>

<?php
get_footer('shop');
