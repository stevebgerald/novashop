<?php
/**
 * Template Name: Quiénes Somos
 */
get_header();
$shop_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/');
?>

<section class="ns-page-hero ns-page-hero--about">
    <div class="ns-container ns-about-hero">
        <nav class="ns-breadcrumbs" aria-label="Breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a>
            <span>/</span>
            <span>Quiénes Somos</span>
        </nav>
        <p class="ns-eyebrow ns-eyebrow--light">NovaShop</p>
        <h1>Innovación y tecnología al alcance de tu mano</h1>
        <p class="ns-lead">
            En NovaShop nos apasiona conectar a las personas con lo último en tecnología.
            Queremos que encuentres los mejores dispositivos con la confianza, rapidez y garantía que te mereces.
        </p>
    </div>
</section>

<section class="ns-section">
    <div class="ns-container ns-about">
        <div class="ns-about__story">
            <p class="ns-kicker">Nuestra historia · Propósito</p>
            <h2>Nacimos con una misión clara: simplificar el acceso a la tecnología de calidad.</h2>
            <p>
                En un mundo que avanza rápido, entendemos que un auricular, un smartwatch o una notebook
                no son solo dispositivos, sino herramientas para trabajar, disfrutar, comunicarte y crear.
                Por eso, en NovaShop nos enfocamos en ofrecer una experiencia de compra transparente,
                ágil y segura de principio a fin.
            </p>
            <p>
                Seleccionamos cada producto de nuestro catálogo bajo estrictos estándares de calidad
                para asegurarnos de que recibas exactamente lo que buscas, respaldado por garantía oficial
                y un equipo dispuesto a asesorarte en todo momento.
            </p>
            <a class="ns-btn ns-btn--navy" href="<?php echo esc_url($shop_url); ?>">Explorar catálogo</a>
        </div>
        <div class="ns-about__visual">
            <img
                src="<?php echo esc_url(NOVASHOP_URI . '/assets/img/hero-devices.png'); ?>"
                alt="Dispositivos NovaShop: notebook, smartphone y wearables"
                width="640"
                height="360"
                loading="lazy"
            >
        </div>
    </div>
</section>

<section class="ns-section ns-section--soft">
    <div class="ns-container">
        <div class="ns-section__head ns-section__head--center">
            <p class="ns-kicker">Nuestros pilares</p>
            <h2>Bloques de valor que nos definen</h2>
            <p class="ns-section__sub">Compromisos concretos en cada compra que hacés en NovaShop.</p>
        </div>

        <div class="ns-about__pillars">
            <article class="ns-pillar">
                <span class="ns-pillar__icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M3 7h13l3 4v6H3V7Z" stroke="currentColor" stroke-width="1.8"/><path d="M16 7v4h4" stroke="currentColor" stroke-width="1.8"/><circle cx="7.5" cy="18.5" r="1.5" fill="currentColor"/><circle cx="16.5" cy="18.5" r="1.5" fill="currentColor"/></svg>
                </span>
                <h3>Envío rápido a todo el país</h3>
                <p>
                    Sabemos que cuando compras tecnología, la quieres cuanto antes.
                    Procesamos y despachamos tu pedido en 24 a 48 horas para que disfrutes sin esperas.
                </p>
            </article>

            <article class="ns-pillar">
                <span class="ns-pillar__icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><rect x="4" y="10" width="16" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="15" r="1.4" fill="currentColor"/></svg>
                </span>
                <h3>Pago 100% seguro</h3>
                <p>
                    Tu tranquilidad es nuestra prioridad. Contamos con plataformas de pago encriptadas
                    para proteger tus datos en cada transacción.
                </p>
            </article>

            <article class="ns-pillar">
                <span class="ns-pillar__icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M12 3l8 3v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-3Z" stroke="currentColor" stroke-width="1.8"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <h3>Garantía y soporte personalizado</h3>
                <p>
                    Todos nuestros productos cuentan con garantía oficial. Además, nuestro equipo de
                    atención al cliente está siempre disponible para resolver tus dudas antes y después
                    de tu compra.
                </p>
            </article>
        </div>
    </div>
</section>

<section class="ns-cta">
    <div class="ns-container ns-cta__inner">
        <h2>¿Listo para encontrar tu próximo dispositivo?</h2>
        <p>Recorré el catálogo o escribinos: te ayudamos a elegir con confianza.</p>
        <div class="ns-cta__actions">
            <a class="ns-btn ns-btn--light" href="<?php echo esc_url($shop_url); ?>">Ir a la tienda</a>
            <a class="ns-btn ns-btn--accent" href="<?php echo esc_url(home_url('/contacto/')); ?>">Contactanos</a>
        </div>
    </div>
</section>

<?php
get_footer();
