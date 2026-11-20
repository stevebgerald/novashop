</main>

<footer class="ns-footer">
    <div class="ns-container ns-footer__grid">
        <div>
            <div class="ns-logo ns-logo--footer">
                <span class="ns-logo__icon" aria-hidden="true">🛍</span>
                <span class="ns-logo__text"><?php bloginfo('name'); ?></span>
            </div>
            <p class="ns-footer__tag">Tecnología que conecta tu mundo. Compra segura, envíos rápidos y soporte personalizado.</p>
        </div>
        <div>
            <h4>Tienda</h4>
            <ul>
                <?php if (function_exists('wc_get_page_id')) : ?>
                    <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Catálogo</a></li>
                    <li><a href="<?php echo esc_url(wc_get_cart_url()); ?>">Carrito</a></li>
                    <li><a href="<?php echo esc_url(wc_get_checkout_url()); ?>">Checkout</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div>
            <h4>Ayuda</h4>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/quienes-somos/')); ?>">Quiénes Somos</a></li>
                <li><a href="<?php echo esc_url(home_url('/contacto/')); ?>">Contacto</a></li>
                <li><a href="#">Envíos</a></li>
                <li><a href="#">Garantía</a></li>
            </ul>
        </div>
        <div>
            <h4>Beneficios</h4>
            <ul class="ns-footer__perks">
                <li>Diseño 100% responsive</li>
                <li>Filtros y ordenamiento</li>
                <li>Compra segura WooCommerce</li>
                <li>Envíos a todo el país</li>
            </ul>
        </div>
    </div>
    <div class="ns-footer__bottom">
        <div class="ns-container">
            <p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. Tienda online con WooCommerce.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
