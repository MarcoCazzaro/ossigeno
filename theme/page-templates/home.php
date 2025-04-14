<?php

/**
 * Template Name: Home
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ossigeno
 */

defined('ABSPATH') || exit;
get_header();
?>
<section id="primary" class="ssnail-home ssnail-no-padding-y">
    <main id="main">
        <?php
        /* Start the Loop */
        while (have_posts()) :
            the_post();
        ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(['ssnail-article', 'page']); ?>>
                <div <?php ssnail_content_class('entry-content'); ?>>
                    <?php the_content(); ?>
                </div><!-- .entry-content -->

                <?php if (get_edit_post_link()) : ?>
                    <footer class="entry-footer">
                        <?php
                        edit_post_link(
                            sprintf(
                                wp_kses(
                                    /* translators: %s: Name of current post. Only visible to screen readers. */
                                    __('Edit <span class="sr-only">%s</span>', 'ossigeno'),
                                    array(
                                        'span' => array(
                                            'class' => array(),
                                        ),
                                    )
                                ),
                                get_the_title()
                            )
                        );
                        ?>
                    </footer><!-- .entry-footer -->
                <?php endif; ?>
            </article><!-- #post-<?php the_ID(); ?> -->
        <?php
        endwhile; // End of the loop.
        ?>
    </main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
