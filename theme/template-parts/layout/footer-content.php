<?php
/**
 * Template part for displaying the footer content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ossigeno
 */

 $menu_locations = get_nav_menu_locations();
 ?>
 <?php
 if (is_active_sidebar('ssnail_ads_footer')) {
 ?>
	 <div class="ssnail-ad-container">
		 <?php dynamic_sidebar("ssnail_ads_footer"); ?>
	 </div>
 <?php
 }
 ?>
 <footer id="colophon" class="site-footer bg-secondary text-white">
	 <div class="w-full px-4 sm:px-6 lg:px-8 py-6 grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
		 <div>
			 <div class="flex items-center gap-3">
				 <a class="flex justify-center items-center w-24" href="<?php echo site_url() ?>">
					 <?php ssnail_get_site_logo(); ?>
				 </a>
				 <h3><?php echo get_bloginfo('name') ?></h3>
			 </div>
 
		 </div>
		 <div>
			 <?php
			 if (is_active_sidebar('ssnail_footer')) {
				 dynamic_sidebar('ssnail_footer');
			 }
			 ?>
		 </div>
		 <div>
			 <?php
			 if (isset($menu_locations['social-menu'])) {
			 ?>
				 <div class="flex flex-col justify-start items-start ssnail-social-navigation">
					 <?php ssnail_print_menu_with_social_icons('social-menu', 'Social', 'hover:text-secondary text-2xl'); ?>
				 </div>
			 <?php
			 }
			 ?>
		 </div>
		 <div>
			 <h3><?php echo __('Navigation', 'ossigeno') ?></h3>
			 <?php
			 $secondary_menu = false;
			 $secondary_menu_items = [];
			 if (isset($menu_locations['footer-menu'])) {
				 $secondary_menu = get_term($menu_locations['footer-menu'], 'nav_menu');
			 }
			 if ($secondary_menu && !is_wp_error($secondary_menu)) {
				 $secondary_menu_items = wp_get_nav_menu_items($secondary_menu);
			 ?>
				 <div class="ssnail-footer-menu">
					 <ul class="ssnail-menu-items list-unstyled d-flex gap-3 flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center mb-0">
						 <?php
						 foreach ($secondary_menu_items as $key => $item) {
						 ?>
							 <li class="ssnail-menu-item mb-3">
								 <a href="<?= $item->url ?>" target="<?= $item->target ?? '' ?>" class="hover:text-primary transition-colors"><?= $item->title ?></a>
							 </li>
						 <?php
						 }
						 ?>
					 </ul>
				 </div>
			 <?php
			 } else if (is_active_sidebar('ssnail_privacy_cookie_widget')) {
			 ?>
				 <div class="d-flex justify-content-center justify-content-lg-start gap-3">
					 <?php dynamic_sidebar("ssnail_privacy_cookie_widget"); ?>
				 </div>
			 <?php
			 }
			 ?>
		 </div>
	 </div>
	 <div class="ssnail-copyright-wrapper">
		 <div class="ssnail-copyright text-center uppercase text-sm pb-3">
			 &copy;<?= date('Y') ?> <?php echo __('All rights reserved', 'ossigeno') ?>
		 </div>
	 </div>
	 <div class="ssnail-theme-by px-3 py-2 flex justify-center items-center bg-black text-white text-sm group">
		 WordPress theme by <a href="https://snappysnail.io" target="_blank" class="inline-flex items-center gap-2 ml-2 transition-colors duration-1000 group-hover:text-[#fcbe03]"><img src="https://snappysnail.io/img/snappysnail-logo.png" class="w-8 h-auto origin-[55%_67%] group-hover:motion-safe:animate-spin"> Snappysnail</a>
	 </div>
 </footer><!-- #colophon -->