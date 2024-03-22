<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the `#content` element and all content thereafter.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;
?>
<?php
if (is_active_sidebar('ssnail_sidebar')) {
?>
    <aside id="sidebar" class="ssnail-sidebar shrink-0 lg:w-64">
        <?php
        dynamic_sidebar('ssnail_sidebar');
        ?>
    </aside>
<?php
}
?>
</div><!-- #content -->

<?php get_template_part('template-parts/layout/footer', 'content'); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

<?php get_template_part('template-parts/layout/floating-buttons'); ?>

</body>

</html>