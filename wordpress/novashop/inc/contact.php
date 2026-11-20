<?php
/**
 * Contact form handler + shortcode/pages helpers.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Process contact form POST.
 */
add_action('init', function () {
    if (empty($_POST['novashop_contact_submit'])) {
        return;
    }
    if (!isset($_POST['novashop_contact_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['novashop_contact_nonce'])), 'novashop_contact')) {
        set_transient('novashop_contact_flash_' . novashop_contact_visitor_key(), ['type' => 'error', 'msg' => 'Sesión inválida. Probá de nuevo.'], 60);
        return;
    }

    $name    = isset($_POST['ns_name']) ? sanitize_text_field(wp_unslash($_POST['ns_name'])) : '';
    $email   = isset($_POST['ns_email']) ? sanitize_email(wp_unslash($_POST['ns_email'])) : '';
    $phone   = isset($_POST['ns_phone']) ? sanitize_text_field(wp_unslash($_POST['ns_phone'])) : '';
    $subject = isset($_POST['ns_subject']) ? sanitize_text_field(wp_unslash($_POST['ns_subject'])) : 'Consulta NovaShop';
    $message = isset($_POST['ns_message']) ? sanitize_textarea_field(wp_unslash($_POST['ns_message'])) : '';

    if ($name === '' || $email === '' || $message === '' || !is_email($email)) {
        set_transient('novashop_contact_flash_' . novashop_contact_visitor_key(), ['type' => 'error', 'msg' => 'Completá nombre, email válido y mensaje.'], 60);
        return;
    }

    $admin_email = get_option('admin_email');
    $body        = "Nombre: $name\nEmail: $email\nTeléfono: $phone\n\nMensaje:\n$message\n";
    $headers     = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    $sent = wp_mail($admin_email, '[NovaShop] ' . $subject, $body, $headers);

    // Always store as private post so the form is "functional" even without mail server.
    wp_insert_post([
        'post_type'    => 'ns_contact',
        'post_title'   => $subject . ' — ' . $name,
        'post_content' => $body,
        'post_status'  => 'private',
        'meta_input'   => [
            '_ns_email' => $email,
            '_ns_phone' => $phone,
        ],
    ]);

    $msg = $sent
        ? '¡Gracias! Tu mensaje fue enviado. Te responderemos pronto.'
        : '¡Gracias! Recibimos tu consulta (guardada en el sitio). Te contactaremos pronto.';

    set_transient('novashop_contact_flash_' . novashop_contact_visitor_key(), ['type' => 'success', 'msg' => $msg], 60);

    wp_safe_redirect(add_query_arg('contacto', 'ok', wp_get_referer() ?: home_url('/contacto')));
    exit;
});

function novashop_contact_visitor_key() {
    return substr(md5(($_SERVER['REMOTE_ADDR'] ?? '') . ($_SERVER['HTTP_USER_AGENT'] ?? '')), 0, 12);
}

function novashop_contact_flash() {
    $key  = 'novashop_contact_flash_' . novashop_contact_visitor_key();
    $data = get_transient($key);
    if ($data) {
        delete_transient($key);
        return $data;
    }
    return null;
}

add_action('init', function () {
    register_post_type('ns_contact', [
        'labels' => [
            'name'          => 'Mensajes de contacto',
            'singular_name' => 'Mensaje',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-email-alt',
        'supports'     => ['title', 'editor'],
        'capability_type' => 'post',
    ]);
});

/**
 * Ensure Contacto + Quiénes Somos pages exist.
 */
function novashop_ensure_pages() {
    $pages = [
        'contacto' => [
            'title'    => 'Contacto',
            'template' => 'page-contacto.php',
            'content'  => '',
        ],
        'quienes-somos' => [
            'title'    => 'Quiénes Somos',
            'template' => 'page-quienes-somos.php',
            'content'  => '',
        ],
    ];

    foreach ($pages as $slug => $data) {
        $page = get_page_by_path($slug);
        if (!$page) {
            // Fallback search by title.
            $found = get_posts([
                'post_type'      => 'page',
                'title'          => $data['title'],
                'post_status'    => 'any',
                'posts_per_page' => 1,
            ]);
            $page = $found ? $found[0] : null;
        }

        if (!$page) {
            $id = wp_insert_post([
                'post_title'   => $data['title'],
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => $data['content'],
            ], true);
        } else {
            $id = (int) $page->ID;
            wp_update_post([
                'ID'          => $id,
                'post_name'   => $slug,
                'post_status' => 'publish',
            ]);
        }

        if ($id && !is_wp_error($id)) {
            update_post_meta($id, '_wp_page_template', $data['template']);
        }
    }

    flush_rewrite_rules(false);
}

add_action('after_switch_theme', 'novashop_ensure_pages');
add_action('init', function () {
    if (is_admin() && current_user_can('manage_options') && isset($_GET['novashop_fix_pages'])) {
        novashop_ensure_pages();
        wp_safe_redirect(admin_url('edit.php?post_type=page&novashop_pages=1'));
        exit;
    }
});
