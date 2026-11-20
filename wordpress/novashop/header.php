<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="ns-topbar">
    <div class="ns-container ns-topbar__inner">
        <p class="ns-topbar__promo">Envío gratis en compras +$50 · Entrega 24–48 hs</p>
        <nav class="ns-topbar__links" aria-label="Ayuda">
            <a href="<?php echo esc_url(home_url('/contacto')); ?>">Ayuda</a>
            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">Seguimiento</a>
            <span class="ns-lang" title="Idioma">ES ▾</span>
        </nav>
    </div>
</div>

<header class="ns-header">
    <div class="ns-container ns-header__row">
        <a class="ns-logo" href="<?php echo esc_url(home_url('/')); ?>">
            <span class="ns-logo__icon" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M6 7h12l-1.2 9.6a2 2 0 0 1-2 1.7H9.2a2 2 0 0 1-2-1.7L6 7Z" stroke="currentColor" stroke-width="1.8"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7" stroke="currentColor" stroke-width="1.8"/><circle cx="9.5" cy="20" r="1.2" fill="currentColor"/><circle cx="15.5" cy="20" r="1.2" fill="currentColor"/></svg>
            </span>
            <span class="ns-logo__text"><?php bloginfo('name'); ?></span>
        </a>

        <form class="ns-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" name="s" placeholder="Buscar productos…" value="<?php echo esc_attr(get_search_query()); ?>" aria-label="Buscar">
            <?php if (class_exists('WooCommerce')) : ?>
                <input type="hidden" name="post_type" value="product">
            <?php endif; ?>
            <button type="submit" aria-label="Buscar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
        </form>

        <div class="ns-header__actions">
            <?php if (function_exists('wc_get_page_permalink')) : ?>
                <a class="ns-icon-btn" href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" aria-label="Cuenta">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/><path d="M5 19.5c1.8-3.2 4.2-4.5 7-4.5s5.2 1.3 7 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </a>
                <a class="ns-icon-btn ns-icon-btn--cart" href="<?php echo esc_url(wc_get_cart_url()); ?>" aria-label="Carrito">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 6h2.2l1.3 9.2a1.5 1.5 0 0 0 1.5 1.3h7.6a1.5 1.5 0 0 0 1.5-1.2L19.5 8H7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="10" cy="20" r="1.2" fill="currentColor"/><circle cx="16.5" cy="20" r="1.2" fill="currentColor"/></svg>
                    <span class="ns-cart-count" data-count="<?php echo esc_attr(novashop_cart_count()); ?>"><?php echo esc_html(novashop_cart_count()); ?></span>
                </a>
            <?php endif; ?>
            <button class="ns-nav-toggle" type="button" aria-label="Menú" aria-expanded="false" data-ns-nav-toggle>
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <div class="ns-nav-bar">
        <div class="ns-container ns-nav-bar__inner">
            <div class="ns-cats">
                <button class="ns-cats__btn" type="button" data-ns-cats-toggle>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Categorías
                </button>
                <div class="ns-cats__menu" hidden data-ns-cats-menu>
                    <?php
                    if (taxonomy_exists('product_cat')) {
                        $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0]);
                        if (!is_wp_error($terms) && $terms) {
                            echo '<ul>';
                            foreach ($terms as $term) {
                                printf(
                                    '<li><a href="%s">%s <span>%d</span></a></li>',
                                    esc_url(get_term_link($term)),
                                    esc_html($term->name),
                                    (int) $term->count
                                );
                            }
                            echo '</ul>';
                        }
                    }
                    ?>
                </div>
            </div>
            <nav class="ns-nav" data-ns-nav aria-label="Principal">
                <?php
                if (has_nav_menu('primary')) {
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'ns-nav__list',
                        'fallback_cb'    => false,
                    ]);
                } else {
                    echo '<ul class="ns-nav__list">';
                    printf('<li><a href="%s">Inicio</a></li>', esc_url(home_url('/')));
                    if (function_exists('wc_get_page_permalink')) {
                        printf('<li><a href="%s">Ofertas</a></li>', esc_url(get_permalink(wc_get_page_id('shop'))));
                        printf('<li><a href="%s">Tienda</a></li>', esc_url(get_permalink(wc_get_page_id('shop'))));
                    }
                    echo '<li><a href="' . esc_url(home_url('/quienes-somos/')) . '">Quiénes Somos</a></li>';
                    echo '<li><a href="' . esc_url(home_url('/contacto/')) . '">Contacto</a></li>';
                    echo '</ul>';
                }
                ?>
            </nav>
        </div>
    </div>
</header>

<main class="ns-main">
