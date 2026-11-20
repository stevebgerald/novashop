<?php
/**
 * One-click demo catalog for NovaShop (admin tool).
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', function () {
    add_theme_page(
        'NovaShop Demo',
        'NovaShop Demo',
        'manage_options',
        'novashop-demo',
        'novashop_demo_page'
    );
});

function novashop_demo_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $message = '';
    if (isset($_POST['novashop_seed']) && check_admin_referer('novashop_seed')) {
        $result  = novashop_seed_catalog();
        $message = sprintf(
            'Listo: %d categorías, %d productos, %d imágenes. Páginas Contacto y Quiénes Somos listas.',
            $result['cats'],
            $result['products'],
            $result['images']
        );
    }
    ?>
    <div class="wrap">
        <h1>NovaShop — Catálogo demo</h1>
        <p>Crea categorías, productos con imágenes, y las páginas <strong>Contacto</strong> y <strong>Quiénes Somos</strong>.</p>
        <?php if ($message) : ?>
            <div class="notice notice-success"><p><?php echo esc_html($message); ?></p></div>
        <?php endif; ?>
        <?php if (!class_exists('WooCommerce')) : ?>
            <div class="notice notice-error"><p>Instalá y activá <strong>WooCommerce</strong> antes de generar el catálogo.</p></div>
        <?php else : ?>
            <form method="post">
                <?php wp_nonce_field('novashop_seed'); ?>
                <p>
                    <button type="submit" name="novashop_seed" class="button button-primary" value="1">
                        Generar / actualizar catálogo + imágenes + páginas
                    </button>
                </p>
            </form>
        <?php endif; ?>
        <hr>
        <h2>Plantillas del tema</h2>
        <ol>
            <li><strong>Inicio</strong> — front-page.php</li>
            <li><strong>Tienda</strong> — woocommerce/archive-product.php</li>
            <li><strong>Producto</strong> — woocommerce/single-product.php</li>
            <li><strong>Carrito</strong> — woocommerce/cart/cart.php</li>
            <li><strong>Checkout</strong> — woocommerce/checkout/form-checkout.php</li>
            <li><strong>Contacto</strong> — page-contacto.php</li>
            <li><strong>Quiénes Somos</strong> — page-quienes-somos.php</li>
        </ol>
    </div>
    <?php
}

/**
 * Attach local theme image to a product as featured image.
 */
function novashop_attach_product_image($product_id, $filename) {
    $path = NOVASHOP_DIR . '/assets/img/' . $filename;
    if (!file_exists($path)) {
        return 0;
    }

    // Reuse existing attachment with same filename if present.
    $existing = get_posts([
        'post_type'      => 'attachment',
        'posts_per_page' => 1,
        'post_status'    => 'inherit',
        'meta_key'       => '_novashop_src',
        'meta_value'     => $filename,
        'fields'         => 'ids',
    ]);
    if ($existing) {
        set_post_thumbnail($product_id, (int) $existing[0]);
        return (int) $existing[0];
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $upload_dir = wp_upload_dir();
    if (!empty($upload_dir['error'])) {
        return 0;
    }

    $dest_name = wp_unique_filename($upload_dir['path'], $filename);
    $dest      = trailingslashit($upload_dir['path']) . $dest_name;
    if (!copy($path, $dest)) {
        return 0;
    }

    $filetype   = wp_check_filetype($dest_name, null);
    $attachment = [
        'post_mime_type' => $filetype['type'],
        'post_title'     => sanitize_file_name(pathinfo($dest_name, PATHINFO_FILENAME)),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ];

    $attach_id = wp_insert_attachment($attachment, $dest, $product_id);
    if (is_wp_error($attach_id) || !$attach_id) {
        return 0;
    }

    $metadata = wp_generate_attachment_metadata($attach_id, $dest);
    wp_update_attachment_metadata($attach_id, $metadata);
    update_post_meta($attach_id, '_novashop_src', $filename);
    set_post_thumbnail($product_id, $attach_id);

    return (int) $attach_id;
}

function novashop_seed_catalog() {
    if (!class_exists('WooCommerce')) {
        return ['cats' => 0, 'products' => 0, 'images' => 0];
    }

    if (function_exists('novashop_ensure_pages')) {
        novashop_ensure_pages();
    }

    $categories = [
        'tecnologia'  => 'Tecnología',
        'audio'       => 'Audio',
        'smartphones' => 'Smartphones',
        'wearables'   => 'Wearables',
        'accesorios'  => 'Accesorios',
    ];

    $cat_ids = [];
    $cats_n  = 0;
    foreach ($categories as $slug => $name) {
        $term = term_exists($slug, 'product_cat');
        if (!$term) {
            $term = wp_insert_term($name, 'product_cat', ['slug' => $slug]);
            $cats_n++;
        }
        if (!is_wp_error($term)) {
            $cat_ids[$slug] = (int) (is_array($term) ? $term['term_id'] : $term);
        }
    }

    $products = [
        [
            'name'     => 'Auriculares Inalámbricos NovaSound Pro',
            'slug'     => 'novasound-pro',
            'regular'  => 129.99,
            'sale'     => 109.99,
            'cat'      => 'audio',
            'featured' => true,
            'image'    => 'earbuds.png',
            'desc'     => 'Cancelación de ruido, batería de 30 h y sonido Hi-Fi. Ideales para trabajo y viaje.',
        ],
        [
            'name'     => 'Smartwatch NovaFit X',
            'slug'     => 'novafit-x',
            'regular'  => 199.00,
            'sale'     => 169.00,
            'cat'      => 'wearables',
            'featured' => true,
            'image'    => 'smartwatch.png',
            'desc'     => 'GPS, monitor cardíaco y resistencia al agua. Pantalla AMOLED brillante.',
        ],
        [
            'name'     => 'Notebook NovaBook Air 14"',
            'slug'     => 'novabook-air-14',
            'regular'  => 899.00,
            'sale'     => 799.00,
            'cat'      => 'tecnologia',
            'featured' => true,
            'image'    => 'laptop.png',
            'desc'     => 'Ultraliviana, SSD 512 GB, 16 GB RAM. Perfecta para estudiar y crear.',
        ],
        [
            'name'     => 'Smartphone NovaPhone 12',
            'slug'     => 'novaphone-12',
            'regular'  => 649.00,
            'sale'     => null,
            'cat'      => 'smartphones',
            'featured' => true,
            'image'    => 'phone.png',
            'desc'     => 'Cámara dual, carga rápida y pantalla fluida 120 Hz.',
        ],
        [
            'name'     => 'Auriculares Over-Ear Studio',
            'slug'     => 'overear-studio',
            'regular'  => 89.00,
            'sale'     => 74.00,
            'cat'      => 'audio',
            'featured' => false,
            'image'    => 'headphones.png',
            'desc'     => 'Drivers de 40 mm, almohadillas memory foam y cable desmontable.',
        ],
        [
            'name'     => 'Parlante Bluetooth NovaBoom',
            'slug'     => 'novaboom',
            'regular'  => 59.99,
            'sale'     => null,
            'cat'      => 'audio',
            'featured' => false,
            'image'    => 'speaker.png',
            'desc'     => 'IPX7, 12 h de batería y graves potentes para exteriores.',
        ],
        [
            'name'     => 'Mouse Ergonómico NovaClick',
            'slug'     => 'novaclick',
            'regular'  => 34.90,
            'sale'     => 29.90,
            'cat'      => 'accesorios',
            'featured' => false,
            'image'    => 'mouse.png',
            'desc'     => 'Silencioso, recargable y sensor 4000 DPI.',
        ],
        [
            'name'     => 'Teclado Mecánico NovaKeys',
            'slug'     => 'novakeys',
            'regular'  => 79.00,
            'sale'     => null,
            'cat'      => 'accesorios',
            'featured' => false,
            'image'    => 'keyboard.png',
            'desc'     => 'Switches táctiles, RGB y layout español.',
        ],
        [
            'name'     => 'Webcam NovaCam 4K',
            'slug'     => 'novacam-4k',
            'regular'  => 119.00,
            'sale'     => 99.00,
            'cat'      => 'tecnologia',
            'featured' => false,
            'image'    => 'webcam.png',
            'desc'     => 'Resolución 4K, autofoco y micrófono dual con reducción de ruido.',
        ],
        [
            'name'     => 'Earbuds NovaBuds Mini',
            'slug'     => 'novabuds-mini',
            'regular'  => 49.00,
            'sale'     => 39.00,
            'cat'      => 'audio',
            'featured' => true,
            'image'    => 'earbuds.png',
            'desc'     => 'Estuche compacto, ANC ligera y app de ecualización.',
        ],
        [
            'name'     => 'Tablet NovaTab 11',
            'slug'     => 'novatab-11',
            'regular'  => 429.00,
            'sale'     => null,
            'cat'      => 'tecnologia',
            'featured' => false,
            'image'    => 'laptop.png',
            'desc'     => 'Pantalla 2K, stylus compatible y batería de larga duración.',
        ],
        [
            'name'     => 'Cargador GaN 65W NovaPower',
            'slug'     => 'novapower-65',
            'regular'  => 39.00,
            'sale'     => 32.00,
            'cat'      => 'accesorios',
            'featured' => false,
            'image'    => 'speaker.png',
            'desc'     => 'Carga notebook + teléfono a la vez. Compacto y seguro.',
        ],
    ];

    $prod_n  = 0;
    $img_n   = 0;
    foreach ($products as $item) {
        $existing = get_page_by_path($item['slug'], OBJECT, 'product');
        if ($existing) {
            $product_id = $existing->ID;
            wp_update_post([
                'ID'           => $product_id,
                'post_title'   => $item['name'],
                'post_content' => $item['desc'],
                'post_excerpt' => $item['desc'],
            ]);
        } else {
            $product_id = wp_insert_post([
                'post_title'   => $item['name'],
                'post_name'    => $item['slug'],
                'post_content' => $item['desc'],
                'post_excerpt' => $item['desc'],
                'post_status'  => 'publish',
                'post_type'    => 'product',
            ]);
            $prod_n++;
        }

        if (is_wp_error($product_id) || !$product_id) {
            continue;
        }

        wp_set_object_terms($product_id, 'simple', 'product_type');
        if (!empty($cat_ids[$item['cat']])) {
            wp_set_object_terms($product_id, [$cat_ids[$item['cat']]], 'product_cat');
        }

        update_post_meta($product_id, '_regular_price', (string) $item['regular']);
        update_post_meta($product_id, '_price', (string) ($item['sale'] ?? $item['regular']));
        if ($item['sale']) {
            update_post_meta($product_id, '_sale_price', (string) $item['sale']);
        } else {
            delete_post_meta($product_id, '_sale_price');
        }
        update_post_meta($product_id, '_manage_stock', 'no');
        update_post_meta($product_id, '_stock_status', 'instock');
        update_post_meta($product_id, '_visibility', 'visible');

        if (!empty($item['image'])) {
            $aid = novashop_attach_product_image($product_id, $item['image']);
            if ($aid) {
                $img_n++;
            }
        }

        $product = wc_get_product($product_id);
        if ($product) {
            $product->set_catalog_visibility('visible');
            $product->set_featured(!empty($item['featured']));
            $product->save();
        }
    }

    if (function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0) {
        update_option('show_on_front', 'page');
        $home = get_page_by_path('inicio');
        if (!$home) {
            $home_id = wp_insert_post([
                'post_title'  => 'Inicio',
                'post_name'   => 'inicio',
                'post_status' => 'publish',
                'post_type'   => 'page',
            ]);
        } else {
            $home_id = $home->ID;
        }
        if ($home_id && !is_wp_error($home_id)) {
            update_option('page_on_front', $home_id);
        }
    }

    // Primary menu with Contacto + Quiénes Somos.
    $menu_name = 'NovaShop Principal';
    $menu      = wp_get_nav_menu_object($menu_name);
    if (!$menu) {
        $menu_id = wp_create_nav_menu($menu_name);
    } else {
        $menu_id = (int) $menu->term_id;
    }
    if (!is_wp_error($menu_id) && $menu_id) {
        $items = wp_get_nav_menu_items($menu_id);
        if (empty($items)) {
            $links = [
                ['title' => 'Inicio', 'url' => home_url('/')],
                ['title' => 'Tienda', 'url' => get_permalink(wc_get_page_id('shop'))],
                ['title' => 'Ofertas', 'url' => get_permalink(wc_get_page_id('shop'))],
                ['title' => 'Quiénes Somos', 'url' => home_url('/quienes-somos/')],
                ['title' => 'Contacto', 'url' => home_url('/contacto/')],
            ];
            foreach ($links as $i => $link) {
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title'  => $link['title'],
                    'menu-item-url'    => $link['url'],
                    'menu-item-status' => 'publish',
                    'menu-item-position' => $i + 1,
                    'menu-item-type'   => 'custom',
                ]);
            }
        }
        $locations = get_theme_mod('nav_menu_locations', []);
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }

    flush_rewrite_rules();

    return ['cats' => $cats_n, 'products' => $prod_n, 'images' => $img_n];
}
