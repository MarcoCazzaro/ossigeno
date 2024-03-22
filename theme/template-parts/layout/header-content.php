<?php

/**
 * Template part for displaying the header content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;
?>
<header id="masthead" class="sticky top-0 z-50 h-12 bg-primary text-white flex justify-between items-center" x-data="{ showMenu: false, showSearch: false }" x-on:keydown.escape="showMenu = false; showSearch = false">
	<div class="w-full lg:h-full px-4 sm:px-6 lg:px-8">
		<div class="relative flex items-center justify-between lg:justify-start lg:h-full">
			<!-- Logo -->
			<div class="flex-shrink-0">
				<a class="flex justify-center items-center w-10" href="<?php echo site_url() ?>">
					<?php ssnail_get_site_logo(); ?>
				</a>
			</div>

			<!-- Search menu button -->
			<div class="absolute inset-y-0 right-12 lg:right-0 flex items-center">
				<button @click="showSearch = !showSearch; showMenu = false;" class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 hover:text-white hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-colors" aria-expanded="false">
					<span class="sr-only">Open search panel</span>
					<span x-show="!showSearch" class="block h-6 w-6" aria-hidden="true"><i class="fas fa-magnifying-glass"></i></span>
					<span x-show="showSearch" class="block h-6 w-6" aria-hidden="true"><i class="fas fa-xmark"></i></span>
				</button>
			</div>

			<!-- Mobile menu button -->
			<div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0 lg:hidden">
				<button @click="showMenu = !showMenu; showSearch = false;" class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 hover:text-white hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-colors" aria-expanded="false">
					<span class="sr-only">Open main menu</span>
					<span x-show="!showMenu" class="block h-6 w-6" aria-hidden="true"><i class="fas fa-bars"></i></span>
					<span x-show="showMenu" class="block h-6 w-6" aria-hidden="true"><i class="fas fa-xmark"></i></span>
				</button>
			</div>

			<nav class="list-none fixed lg:relative top-12 lg:top-0 left-0 w-full lg:w-auto lg:h-full flex z-10 transition-all" x-bind:class="{ 'opacity-100 h-auto': showMenu, 'opacity-0 lg:opacity-100 h-0 lg:h-auto overflow-clip lg:overflow-visible' : !showMenu }">
				<?php
				wp_nav_menu(array(
					'theme_location' => 'primary-menu',
					'fallback_cb' => false,
					'container' => false,
					'menu_class' => 'menu tailwind-menu',
				));
				?>
			</nav>

			<div class="ssnail-search-panel fixed top-12 left-0 w-full flex z-10 transition-all" x-bind:class="{ 'opacity-100 h-auto': showSearch, 'opacity-0 h-0 overflow-clip' : !showSearch }">
				<div class="bg-secondary text-white px-4 sm:px-6 lg:px-8 py-4 w-full">
					<?php get_search_form(); ?>
				</div>
			</div>
		</div>
	</div>
</header><!-- #masthead -->