<?php
/**
 * Template part for displaying the site header / navigation
 *
 * @package Ossigeno
 */

?>
<nav id="masthead"
	x-data="{ open: false, searchOpen: false, scrolled: false }"
	@scroll.window.debounce.100ms="scrolled = window.scrollY > 10"
	:class="(scrolled || open || searchOpen) ? 'bg-primary' : 'bg-transparent'"
	class="ssnail-navigation fixed top-0 w-full z-50 transition-colors duration-300">

	<?php
	/*
	 * Three-column CSS grid (1fr auto 1fr): the outer columns share space equally
	 * so the centre column (nav) is always mathematically centred regardless of
	 * how wide the logo or the right-side controls happen to be.
	 */
	?>
	<div class="grid grid-cols-2 md:grid-cols-[1fr_auto_1fr] items-center px-6 md:px-12 py-6">

		<!-- Logo — col 1 -->
		<div class="flex items-center">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="text-background font-headings text-xl">
					<?php bloginfo( 'name' ); ?>
				</a>
				<?php
			}
			?>
		</div>

		<!-- Desktop navigation — col 2, centred -->
		<div class="hidden md:flex items-center" aria-label="<?php esc_attr_e( 'Main Navigation', 'ossigeno' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary-menu',
					'container'      => false,
					'menu_id'        => 'nav-primary',
					'menu_class'     => 'flex space-x-8 items-center',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>

		<!-- Right controls: search + hamburger — col 3 -->
		<div class="flex items-center gap-3 justify-end">

			<button
				@click="searchOpen = !searchOpen"
				:aria-expanded="searchOpen.toString()"
				aria-label="<?php esc_attr_e( 'Search', 'ossigeno' ); ?>"
				class="inline-flex items-center text-background/80 hover:text-secondary transition-colors">
				<span class="material-symbols-outlined" aria-hidden="true">search</span>
			</button>

			<button
				@click="open = !open"
				:aria-expanded="open.toString()"
				aria-controls="nav-mobile"
				aria-label="<?php esc_attr_e( 'Menu', 'ossigeno' ); ?>"
				class="md:hidden text-background/80 hover:text-secondary transition-colors">
				<span class="material-symbols-outlined" aria-hidden="true" x-text="open ? 'close' : 'menu'">menu</span>
			</button>

		</div>
	</div>

	<!-- Mobile navigation panel -->
	<div id="nav-mobile"
		x-show="open"
		x-transition:enter="transition ease-out duration-200"
		x-transition:enter-start="opacity-0 -translate-y-2"
		x-transition:enter-end="opacity-100 translate-y-0"
		x-transition:leave="transition ease-in duration-150"
		x-transition:leave-start="opacity-100 translate-y-0"
		x-transition:leave-end="opacity-0 -translate-y-2"
		class="md:hidden bg-primary px-6 pb-6">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary-menu',
				'container'      => false,
				'menu_id'        => 'nav-mobile-list',
				'menu_class'     => 'flex flex-col space-y-4 pt-4',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		?>
	</div>

	<!-- Search panel -->
	<div x-show="searchOpen"
		x-transition:enter="transition ease-out duration-200"
		x-transition:enter-start="opacity-0 -translate-y-2"
		x-transition:enter-end="opacity-100 translate-y-0"
		x-transition:leave="transition ease-in duration-150"
		x-transition:leave-start="opacity-100 translate-y-0"
		x-transition:leave-end="opacity-0 -translate-y-2"
		class="bg-primary px-6 md:px-12 py-4">
		<?php get_search_form(); ?>
	</div>

</nav>
