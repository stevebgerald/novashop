<?php
/**
 * Fallback blog / pages index.
 */
get_header();
?>
<div class="ns-container ns-content">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article <?php post_class('ns-page'); ?>>
            <h1><?php the_title(); ?></h1>
            <div class="ns-page__body"><?php the_content(); ?></div>
        </article>
    <?php endwhile; else : ?>
        <p>No hay contenido.</p>
    <?php endif; ?>
</div>
<?php
get_footer();
