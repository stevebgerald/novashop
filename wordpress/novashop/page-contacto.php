<?php
/**
 * Template Name: Contacto
 * Formulario de contacto funcional.
 */
get_header();

$flash = novashop_contact_flash();
?>

<section class="ns-page-hero">
    <div class="ns-container">
        <nav class="ns-breadcrumbs" aria-label="Breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a>
            <span>/</span>
            <span>Contacto</span>
        </nav>
        <h1>Contacto</h1>
        <p class="ns-lead">¿Tenés una consulta sobre un producto, envío o garantía? Escribinos y te respondemos a la brevedad.</p>
    </div>
</section>

<section class="ns-section">
    <div class="ns-container ns-contact">
        <div class="ns-contact__info">
            <h2>Estamos para ayudarte</h2>
            <ul class="ns-contact__list">
                <li>
                    <strong>Email</strong>
                    <span><?php echo esc_html(get_option('admin_email')); ?></span>
                </li>
                <li>
                    <strong>Horario</strong>
                    <span>Lunes a viernes · 9:00 a 18:00</span>
                </li>
                <li>
                    <strong>Soporte</strong>
                    <span>Envíos, pagos y garantía oficial</span>
                </li>
                <li>
                    <strong>Tienda</strong>
                    <span>Tecnología con entrega a todo el país</span>
                </li>
            </ul>
            <div class="ns-contact__perks">
                <p>✓ Respuesta en menos de 24 hs hábiles</p>
                <p>✓ Asesoramiento de producto</p>
                <p>✓ Seguimiento de pedidos</p>
            </div>
        </div>

        <div class="ns-contact__form-wrap">
            <?php if ($flash) : ?>
                <div class="ns-alert ns-alert--<?php echo esc_attr($flash['type']); ?>">
                    <?php echo esc_html($flash['msg']); ?>
                </div>
            <?php endif; ?>

            <form class="ns-contact-form" method="post" action="">
                <?php wp_nonce_field('novashop_contact', 'novashop_contact_nonce'); ?>
                <div class="ns-form-row">
                    <label for="ns_name">Nombre completo *</label>
                    <input type="text" id="ns_name" name="ns_name" required placeholder="Tu nombre">
                </div>
                <div class="ns-form-grid">
                    <div class="ns-form-row">
                        <label for="ns_email">Email *</label>
                        <input type="email" id="ns_email" name="ns_email" required placeholder="tu@email.com">
                    </div>
                    <div class="ns-form-row">
                        <label for="ns_phone">Teléfono</label>
                        <input type="tel" id="ns_phone" name="ns_phone" placeholder="+595 ...">
                    </div>
                </div>
                <div class="ns-form-row">
                    <label for="ns_subject">Asunto</label>
                    <input type="text" id="ns_subject" name="ns_subject" placeholder="Consulta sobre un producto">
                </div>
                <div class="ns-form-row">
                    <label for="ns_message">Mensaje *</label>
                    <textarea id="ns_message" name="ns_message" rows="6" required placeholder="Contanos en qué podemos ayudarte..."></textarea>
                </div>
                <button type="submit" name="novashop_contact_submit" value="1" class="ns-btn ns-btn--accent">
                    Enviar mensaje →
                </button>
            </form>
        </div>
    </div>
</section>

<?php
get_footer();
