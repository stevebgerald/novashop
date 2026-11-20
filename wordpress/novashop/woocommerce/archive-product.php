<?php
/**
 * Template: Tienda / Catálogo — filtros + grilla de productos
 * @package NovaShop
 */

defined('ABSPATH') || exit;

get_header('shop');

$shop_url = get_permalink(wc_get_page_id('shop'));
?>

<div class="ns-container ns-shop">
    <nav class="ns-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a>
        <span>/</span>
        <span>Tienda</span>
    </nav>

    <header class="ns-shop__head">
        <h1><?php woocommerce_page_title(); ?></h1>
        <?php
        global $wp_query;
        $total = (int) $wp_query->found_posts;
        $per   = (int) $wp_query->get('posts_per_page');
        $page  = max(1, (int) $wp_query->get('paged'));
        $from  = $total ? (($page - 1) * $per) + 1 : 0;
        $to    = min($page * $per, $total);
        ?>
        <p class="ns-shop__count">
            <?php
            printf(
                esc_html__('Mostrando %1$d–%2$d de %3$d resultados', 'novashop'),
                $from,
                $to,
                $total
            );
            ?>
        </p>
    </header>

    <div class="ns-shop__layout">
        <aside class="ns-filters" aria-label="Filtros">
            <h2>Categorías</h2>
            <ul class="ns-filters__cats">
                <?php
                $terms = get_terms([
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                    'parent'     => 0,
                ]);
                $current = get_queried_object_id();
                if (!is_wp_error($terms)) {
                    printf(
                        '<li class="%s"><a href="%s">Todos</a></li>',
                        is_shop() ? 'is-active' : '',
                        esc_url($shop_url)
                    );
                    foreach ($terms as $term) {
                        printf(
                            '<li class="%s"><a href="%s">%s <span>%d</span></a></li>',
                            $current === (int) $term->term_id ? 'is-active' : '',
                            esc_url(get_term_link($term)),
                            esc_html($term->name),
                            (int) $term->count
                        );
                    }
                }
                ?>
            </ul>

            <h2>Filtrar por precio</h2>
            <form class="ns-price-filter" method="get" action="<?php echo esc_url($shop_url); ?>">
                <?php
                // Preserve category if on category archive.
                if (is_product_category()) {
                    echo '<input type="hidden" name="product_cat" value="' . esc_attr(get_queried_object()->slug) . '">';
                }
                $min = isset($_GET['min_price']) ? sanitize_text_field(wp_unslash($_GET['min_price'])) : '';
                $max = isset($_GET['max_price']) ? sanitize_text_field(wp_unslash($_GET['max_price'])) : '';
                ?>
                <div class="ns-price-filter__row">
                    <label>Min <input type="number" name="min_price" min="0" step="1" value="<?php echo esc_attr($min); ?>" placeholder="0"></label>
                    <label>Max <input type="number" name="max_price" min="0" step="1" value="<?php echo esc_attr($max); ?>" placeholder="999"></label>
                </div>
                <button type="submit" class="ns-btn ns-btn--navy ns-btn--sm">Filtrar</button>
            </form>

            <h2>Ordenar por</h2>
            <form class="ns-sort" method="get">
                <?php
                foreach ($_GET as $key => $val) {
                    if (in_array($key, ['orderby'], true)) {
                        continue;
                    }
                    if (is_array($val)) {
                        continue;
                    }
                    printf('<input type="hidden" name="%s" value="%s">', esc_attr($key), esc_attr(wp_unslash($val)));
                }
                $orderby = isset($_GET['orderby']) ? sanitize_text_field(wp_unslash($_GET['orderby'])) : 'menu_order';
                $options = [
                    'menu_order' => 'Destacados',
                    'popularity' => 'Más vendidos',
                    'date'       => 'Nuevos',
                    'price'      => 'Precio: menor a mayor',
                    'price-desc' => 'Precio: mayor a menor',
                ];
                foreach ($options as $value => $label) {
                    printf(
                        '<label class="ns-sort__opt"><input type="radio" name="orderby" value="%s" %s onchange="this.form.submit()"> %s</label>',
                        esc_attr($value),
                        checked($orderby, $value, false),
                        esc_html($label)
                    );
                }
                ?>
            </form>
        </aside>

        <div class="ns-shop__main">
            <?php if (woocommerce_product_loop()) : ?>
                <div class="ns-grid ns-grid--3">
                    <?php
                    while (have_posts()) {
                        the_post();
                        novashop_product_card();
                    }
                    ?>
                </div>
                <div class="ns-pagination">
                    <?php woocommerce_pagination(); ?>
                </div>
            <?php else : ?>
                <?php do_action('woocommerce_no_products_found'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
get_footer('shop');
