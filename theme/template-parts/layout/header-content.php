<?php

/**
 * Template part for displaying the header content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ossigeno
 */

$menu_locations = get_nav_menu_locations();
if (is_registered_sidebar('ssnail_ads_masthead')) {
?>
	<div class="ssnail-ads-wrapper relative z-50 bg-primary border-b border-gray-600 grid place-content-center">
		<?php dynamic_sidebar('ssnail_ads_masthead'); ?>
	</div>
<?php
}
?>
<header id="masthead" class="ssnail-navigation sticky top-0 z-40 h-12" x-ref="navbar" x-data="{
		 showMenu: false,
		 showSearch: false,
		 toggleSearch() { this.setSearchPosition(); this.showSearch = !this.showSearch; this.showMenu = false; }, 
		 toggleMenu() { 
			 if (!this.showMenu) {
				 this.showSearch = false;
				 this.scrollToTop();
				 setTimeout(() => {
					 this.showMenu = true;
					 document.body.classList.add('overflow-hidden');
				 }, 150);
			 } else {
				 this.showMenu = false;
				 document.body.classList.remove('overflow-hidden');
			 }
		 },
		 scrollToTop() {
			 document.querySelector('header').scrollIntoView({ behavior: 'smooth' });
		 },
		 setSearchPosition() {
			 const rect = $refs.navbar.getBoundingClientRect();
			 $refs.searchPanel.style.top = `${rect.bottom}px`;
		 },
		 setMenuPosition() {
			console.log('setMenuPosition');
			 const rect = $refs.navbar.getBoundingClientRect();
			 const navbarBottom = rect.height + rect.top;
			 const visibleHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
			 if (window.getComputedStyle($refs.mainMenu).position === 'fixed') {
				 $refs.mainMenu.style.top = `${navbarBottom}px`;
				 $refs.mainMenu.style.height = `calc(${visibleHeight}px - ${navbarBottom}px)`;
			 } else {
				 $refs.mainMenu.style.top = 'auto';
				 $refs.mainMenu.style.height = '';
			 }
		 }
	 }" x-init="setMenuPosition()" x-on:keydown.escape="showMenu = false; showSearch = false" x-on:resize.window="setMenuPosition();setSearchPosition();" x-on:scroll.window.debounce.50ms="setSearchPosition()" x-on:scrollend.window="setMenuPosition();">

	<div class="bg-primary w-full h-full px-4 sm:px-6 lg:px-8">
		<div class="relative flex items-center justify-between lg:justify-start h-full">
			<!-- Logo -->
			<div class="h-full flex-shrink-0 flex items-center">
				<a class="flex justify-center items-center w-36" href="<?php echo site_url() ?>">
					<?php ssnail_get_site_logo('h-8 w-auto'); ?>
				</a>
			</div>

			<!-- Search menu button -->
			<div class="absolute inset-y-0 right-12 lg:right-0 flex items-center">
				<button @click="toggleSearch()" class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 hover:text-white hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-colors" aria-expanded="false">
					<span class="sr-only">Open search panel</span>
					<span x-show="!showSearch" class="block h-6 w-6" aria-hidden="true"><span class="material-symbols-outlined">search</span></span>
					<span x-show="showSearch" class="block h-6 w-6" aria-hidden="true"><span class="material-symbols-outlined">close</span></span>
				</button>
			</div>

			<!-- Login button -->
			<div class="absolute hidden lg:flex inset-y-0 lg:right-12 lg:items-center">
				<a href="<?php echo home_url("my-ssnail"); ?>" class="flex items-center text-white hover:text-secondary transition-colors"><span class="text-sm">Accedi</span><span class="material-symbols-outlined ml-2">account_circle</span></a>
			</div>

			<!-- Mobile menu button -->
			<div class="absolute inset-y-0 right-0 flex items-center sm:static sm:inset-auto sm:ml-6 sm:pr-0 lg:hidden">
				<button @click="toggleMenu()" class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 hover:text-white hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-colors" aria-expanded="false">
					<span class="sr-only">Open main menu</span>
					<span x-show="!showMenu" class="block h-6 w-6" aria-hidden="true"><span class="material-symbols-outlined">menu</span></span>
					<span x-show="showMenu" class="block h-6 w-6" aria-hidden="true"><span class="material-symbols-outlined">close</span></span>
				</button>
			</div>

			<nav class="list-none fixed lg:relative top-12 lg:top-0 left-0 w-full lg:w-auto lg:max-w-[calc(100vw-400px)] lg:h-full flex flex-col lg:flex-row lg:grow z-10 transition-transform bg-primary" x-cloak x-bind:class="{ 'opacity-100 translate-x-0': showMenu, 'opacity-0 lg:opacity-100 -translate-x-full lg:translate-x-0 overflow-clip lg:overflow-visible' : !showMenu }" x-ref="mainMenu">
				<div class="ssnail-menu-wrapper h-[calc(100%-theme(spacing.48))] lg:h-auto lg:grow overflow-y-auto lg:overflow-y-visible overflow-x-clip lg:overflow-x-clip pb-32 lg:pb-0 pt-3 lg:pt-0">
					<?php
					wp_nav_menu(array(
						'theme_location' => 'primary-menu',
						'fallback_cb' => false,
						'container' => false,
						'menu_class' => 'menu tailwind-menu',
					));
					?>
				</div>

				<div class="ssnail-menu-footer relative flex flex-col lg:hidden h-48 py-2 px-4">
					<div class="w-full border-t border-gray-600 py-4">
						<a href="<?php echo home_url("my-ssnail"); ?>" class="flex items-center text-secondary hover:text-white transition-colors"><span class="material-symbols-outlined mr-2">account_circle</span>Accedi</a>
					</div>
					<?php
					if (isset($menu_locations['social-menu'])) {
					?>
						<div class="ssnail-social-navigation w-full h-full border-t border-gray-600 text-secondary text-3xl flex items-center">
							<?php ssnail_print_menu_with_social_icons('social-menu', ''); ?>
						</div>
					<?php
					}
					?>
				</div>
			</nav>

			<div class="ssnail-search-panel fixed -z-10 top-0 left-0 w-full flex overflow-clip transition-transform transition-[top]" x-cloak x-bind:class="{ 'opacity-100 translate-y-0 h-auto': showSearch, 'opacity-0 -translate-y-full h-0' : !showSearch }" x-ref="searchPanel">
				<div class="bg-secondary text-white px-4 sm:px-6 lg:px-8 py-4 w-full">
					<?php get_search_form(); ?>
				</div>
			</div>
		</div>
	</div>
</header><!-- #masthead -->