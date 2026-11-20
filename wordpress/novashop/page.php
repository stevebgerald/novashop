<?php
/**
 * Generic page template.
 */
get_header();

$is_woo_endpoint = function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page());
?>
<div class="ns-container ns-content">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class($is_woo_endpoint ? 'ns-page ns-page--woo' : 'ns-page'); ?>>
            <?php if (!$is_woo_endpoint) : ?>
                <h1><?php the_title(); ?></h1>
            <?php endif; ?>
            <div class="ns-page__body"><?php the_content(); ?></div>
        </article>
    <?php endwhile; ?>
</div>
<?php
get_footer();
