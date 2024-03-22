<?php

/**
 * Functions for custom widget areas - ads placement
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;

function ssnail_register_widgets_areas()
{
	register_sidebar(array(
		'name'          => 'Sidebar',
		'id'            => 'ssnail_sidebar',
		'before_widget' => '<div class="ssnail-sidebar-area">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	));

	register_sidebar(array(
		'name'          => 'Footer',
		'id'            => 'ssnail_footer',
		'before_widget' => '<div class="ssnail-footer-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	));

	register_sidebar(array(
		'name'          => 'Ads - Footer',
		'id'            => 'ssnail_ads_footer',
		'before_widget' => '<div class="ssnail-ads footer">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	));
}
add_action('widgets_init', 'ssnail_register_widgets_areas');
