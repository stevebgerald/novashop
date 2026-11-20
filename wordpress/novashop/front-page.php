<?php
/**
 * Template: Inicio — Hero + productos destacados
 */
get_header();
$shop_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/');
?>

<section class="ns-hero">
    <div class="ns-container ns-hero__grid">
        <div class="ns-hero__copy">
            <p class="ns-eyebrow">NovaShop · Tecnología</p>
            <h1>Tecnología que conecta tu mundo</h1>
            <p class="ns-hero__lead">Calidad, precio y envíos rápidos. Descubrí auriculares, smartphones, notebooks y más con garantía oficial.</p>
            <a class="ns-btn ns-btn--accent" href="<?php echo esc_url($shop_url); ?>">Ver ofertas →</a>
        </div>
        <div class="ns-hero__visual">
            <img
                class="ns-hero__img"
                src="<?php echo esc_url(NOVASHOP_URI . '/assets/img/hero-devices.png'); ?>"
                alt="Notebook, smartphone, auriculares y smartwatch NovaShop"
                width="800"
                height="450"
                loading="eager"
            >
        </div>
    </div>
</section>

<section class="ns-trust">
    <div class="ns-container ns-trust__row">
        <div class="ns-trust__item">
            <span class="ns-trust__icon">⚡</span>
            <div>
                <strong>Envío rápido</strong>
                <p>A todo el país</p>
            </div>
        </div>
        <div class="ns-trust__item">
            <span class="ns-trust__icon">🔒</span>
            <div>
                <strong>Pago seguro</strong>
                <p>Compra protegida</p>
            </div>
        </div>
        <div class="ns-trust__item">
            <span class="ns-trust__icon">✓</span>
            <div>
                <strong>Garantía oficial</strong>
                <p>Soporte personalizado</p>
            </div>
        </div>
    </div>
</section>

<section class="ns-section">
    <div class="ns-container">
        <div class="ns-section__head">
            <h2>Productos destacados</h2>
            <a class="ns-link" href="<?php echo esc_url($shop_url); ?>">Ver catálogo</a>
        </div>

        <?php if (class_exists('WooCommerce')) : ?>
            <div class="ns-grid ns-grid--4">
                <?php
                $q = new WP_Query([
                    'post_type'      => 'product',
                    'posts_per_page' => 4,
                    'tax_query'      => [
                        [
                            'taxonomy' => 'product_visibility',
                            'field'    => 'name',
                            'terms'    => ['featured'],
                            'operator' => 'IN',
                        ],
                    ],
                ]);
                if (!$q->have_posts()) {
                    $q = new WP_Query([
                        'post_type'      => 'product',
                        'posts_per_page' => 4,
                    ]);
                }
                while ($q->have_posts()) {
                    $q->the_post();
                    novashop_product_card();
                }
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            <p class="ns-notice">Activá <strong>WooCommerce</strong> y generá el catálogo desde Apariencia → NovaShop Demo.</p>
        <?php endif; ?>
    </div>
</section>

<section class="ns-cta">
    <div class="ns-container ns-cta__inner">
        <h2>Filtros avanzados, carrito y checkout listos</h2>
        <p>Explorá el catálogo, ordená por precio y completá tu compra en minutos.</p>
        <a class="ns-btn ns-btn--light" href="<?php echo esc_url($shop_url); ?>">Ir a la tienda</a>
    </div>
</section>

<?php
get_footer();
