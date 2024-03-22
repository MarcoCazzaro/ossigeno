<?php

/**
 * Template part for displaying search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;
$layout = $args['layout'] ?? 'list';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(['ssnail-article', 'archive', 'search', 'grid', 'gap-6', 'group', $layout]); ?>>
    <div class="ssnail-image-wrapper">
        <?php ssnail__post_thumbnail(null, 'medium'); ?>
    </div>
    <div class="ssnail-text-wrapper md:col-span-3">
        <header class="entry-header">
            <?php ssnail__post_categories(); ?>
            <?php
            if (is_sticky() && is_home() && !is_paged()) {
                printf('%s', esc_html_x('Featured', 'post', 'ossigeno'));
            }
            the_title(sprintf('<h2 class="entry-title group-hover:text-primary transition-colors mt-4"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>');
            ?>
        </header><!-- .entry-header -->

        <div <?php ssnail__content_class('entry-content mb-4 text-xs'); ?>>
            <?php the_excerpt(); ?>
        </div><!-- .entry-content -->

    </div>
</article><!-- #post-${ID} -->