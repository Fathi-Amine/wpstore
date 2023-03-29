<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Starter Register Demos
 */
function botiga_demos_list() {

	$plugins = array();

	$plugins[] = array(
		'name'     => 'WooCommerce',
		'slug'     => 'woocommerce',
		'path'     => 'woocommerce/woocommerce.php',
		'required' => true
	);

	$demos = array(
		'beauty'      => array(
			'name'       => esc_html__( 'Beauty', 'botiga' ),
			'type'       => 'free',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/beauty/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					)
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/beauty/botiga-dc-beauty.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/beauty/botiga-w-beauty.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/beauty/botiga-c-beauty.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/beauty/botiga-dc-beauty-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/beauty/botiga-w-beauty-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/beauty/botiga-c-beauty-el.dat'
				),
			),
		),
		'apparel'   => array(
			'name'       => esc_html__( 'Apparel', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-apparel/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/apparel/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					)
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/apparel/botiga-dc-apparel.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/apparel/botiga-w-apparel.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/apparel/botiga-c-apparel.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/apparel/botiga-dc-apparel-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/apparel/botiga-w-apparel-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/apparel/botiga-c-apparel-el.dat'
				),
			),
		),
		'furniture'   => array(
			'name'       => esc_html__( 'Furniture', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-furniture/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/furniture/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					)					
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/furniture/botiga-dc-furniture.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/furniture/botiga-w-furniture.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/furniture/botiga-c-furniture.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/furniture/botiga-dc-furniture-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/furniture/botiga-w-furniture-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/furniture/botiga-c-furniture-el.dat'
				),
			),
		),
		'jewelry'   => array(
			'name'       => esc_html__( 'Jewelry', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-jewelry/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/jewelry/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					)					
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/jewelry/botiga-dc-jewelry.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/jewelry/botiga-w-jewelry.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/jewelry/botiga-c-jewelry.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/jewelry/botiga-dc-jewelry-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/jewelry/botiga-w-jewelry-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/jewelry/botiga-c-jewelry-el.dat'
				),
			),
		),
		'single-product'   => array(
			'name'       => esc_html__( 'Single Product', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-single-product/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/single-product/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					)					
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/single-product/botiga-dc-single-product.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/single-product/botiga-w-single-product.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/single-product/botiga-c-single-product.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/single-product/botiga-dc-single-product-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/single-product/botiga-w-single-product-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/single-product/botiga-c-single-product-el.dat'
				),
			),
		),
		'multi-vendor' => array(
			'name'       => esc_html__( 'Multi Vendor', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-multi-vendor/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/multi-vendor/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					),
					array(
						'name'     => 'Dokan',
						'slug'     => 'dokan-lite',
						'path'     => 'dokan-lite/dokan.php',
						'required' => false
					)
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/multi-vendor/botiga-dc-multi-vendor.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/multi-vendor/botiga-w-multi-vendor.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/multi-vendor/botiga-c-multi-vendor.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/multi-vendor/botiga-dc-multi-vendor-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/multi-vendor/botiga-w-multi-vendor-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/multi-vendor/botiga-c-multi-vendor-el.dat'
				),
			),
		),
		'wine' => array(
			'name'       => esc_html__( 'Wine', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-wine/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/wine/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					),
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/wine/botiga-dc-wine.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/wine/botiga-w-wine.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/wine/botiga-c-wine.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/wine/botiga-dc-wine-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/wine/botiga-w-wine-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/wine/botiga-c-wine-el.dat'
				),
			),
		),
		'plants' => array(
			'name'       => esc_html__( 'Plants', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-plants/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/plants/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					),
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/plants/botiga-dc-plants.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/plants/botiga-w-plants.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/plants/botiga-c-plants.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/plants/botiga-dc-plants-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/plants/botiga-w-plants-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/plants/botiga-c-plants-el.dat'
				),
			),
		),
	);

	return $demos;

}
add_filter( 'atss_register_demos_list', 'botiga_demos_list' );

/**
 * Define actions that happen after import
 */
function botiga_setup_after_import( $demo_id ) {

	// Assign the menu.
	$main_menu = get_term_by( 'name', 'Main', 'nav_menu' );
	if ( ! empty( $main_menu ) ) {
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary'] = $main_menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// Beauty, Furniture and Single Product Demo Extras
	if ( in_array( $demo_id, array( 'beauty', 'furniture', 'single-product', 'multi-vendor' ) ) ) {

		// Set modules.
	  $modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 'hf-builder' => true ) ) );

	}

	// Multi Vendor Demo Extras
	if ( $demo_id === 'multi-vendor' ) {

		// Set modules.
	  $modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 'hf-builder' => true, 'mega-menu' => true, 'size-chart' => true, 'product-swatches' => true ) ) );

		// Assign secondary menu
		$secondary_menu = get_term_by( 'name', 'Trending Categories', 'nav_menu' );
		if ( ! empty( $secondary_menu ) ) {
			$locations = get_theme_mod( 'nav_menu_locations', array() );
			$locations['secondary'] = $secondary_menu->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}

	}

	// Apparel Demo Extras
	if ( $demo_id === 'apparel' ) {

		// Set modules.
		// The demo apparel uses the old header system, so we need to disable the HF Builder
	  $modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 'hf-builder' => false ) ) );

		// Assign footer copyright menu
		$copyright_menu = get_term_by( 'name', 'Footer Copyright', 'nav_menu' );
		if ( ! empty( $copyright_menu ) ) {
			$locations = get_theme_mod( 'nav_menu_locations', array() );
			$locations['footer-copyright-menu'] = $copyright_menu->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}

	}

	// Jewelry Demo Extras
	if ( $demo_id === 'jewelry' ) {

		// Set modules.
	  	$modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 'hf-builder' => true, 'mega-menu' => true ) ) );

		// Update custom CSS file with mega menu css
		if ( class_exists( 'Botiga_Mega_menu' ) ) {
			$mega_menu = Botiga_Mega_Menu::get_instance();
			$mega_menu->save_mega_menu_css_as_option();
			$mega_menu->update_custom_css_file();
		}

	}

	if( $demo_id === 'plants' ) {
		// Set modules.
		$modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 'wishlist' => true, 'advanced-reviews' => true ) ) );
	}

	// "Footer" menu (menu name from import)
	$footer_menu_one = get_term_by( 'name', 'Footer', 'nav_menu' );
	if ( ! empty( $footer_menu_one ) ) {
		$nav_menu_widget = get_option( 'widget_nav_menu' );
		foreach ( $nav_menu_widget as $key => $widget ) {
			if ( $key !== '_multiwidget' ) {
				if ( ( ! empty( $nav_menu_widget[ $key ]['title'] ) && in_array( $nav_menu_widget[ $key ]['title'], array( 'Quick links', 'Quick Links' ) ) ) || ( empty( $nav_menu_widget[ $key ]['title'] ) && $demo_id === 'jewelry' ) || ( empty( $nav_menu_widget[ $key ]['title'] ) && $demo_id === 'wine' ) ) {
					$nav_menu_widget[ $key ]['nav_menu'] = $footer_menu_one->term_id;
					update_option( 'widget_nav_menu', $nav_menu_widget );
				}
			}
		}
	}

	// "Footer 2" menu (menu name from import)
	$footer_menu_two = get_term_by( 'name', 'Footer 2', 'nav_menu' );
	if ( ! empty( $footer_menu_two ) ) {
		$nav_menu_widget = get_option( 'widget_nav_menu' );
		foreach ( $nav_menu_widget as $key => $widget ) {
			if ( $key !== '_multiwidget' ) {
				if ( ! empty( $nav_menu_widget[ $key ]['title'] ) && in_array( $nav_menu_widget[ $key ]['title'], array( 'About' ) ) ) {
					$nav_menu_widget[ $key ]['nav_menu'] = $footer_menu_two->term_id;
					update_option( 'widget_nav_menu', $nav_menu_widget );
				}
			}
		}
	}

	// Asign the front as page.
	update_option( 'show_on_front', 'page' );

	// Asign the front page.
	$front_page = get_page_by_title( 'Home' );
	if ( ! empty( $front_page ) ) {
		update_option( 'page_on_front', $front_page->ID );
	}

	// Asign the blog page.
	$blog_page  = get_page_by_title( 'Blog' );
	if ( ! empty( $blog_page ) ) {
		update_option( 'page_for_posts', $blog_page->ID );
	}

	// My wishlist page
	$wishlist_page = get_page_by_title( 'My Wishlist' );
	if ( ! empty( $wishlist_page ) ) {
		update_option( 'botiga_wishlist_page_id', $wishlist_page->ID );
	}

	// Asign the shop page.
	$shop_page = ( 'single-product' === $demo_id ) ? get_page_by_title( 'Listing' ) : get_page_by_title( 'Shop' );
	if ( ! empty( $shop_page ) ) {
		update_option( 'woocommerce_shop_page_id', $shop_page->ID );
	}

	// Asign the cart page.
	$cart_page = get_page_by_title( 'Cart' );
	if ( ! empty( $cart_page ) ) {
		update_option( 'woocommerce_cart_page_id', $cart_page->ID );
	}

	// Asign the checkout page.
	$checkout_page  = get_page_by_title( 'Checkout' );
	if ( ! empty( $checkout_page ) ) {
		update_option( 'woocommerce_checkout_page_id', $checkout_page->ID );
	}

	// Asign the myaccount page.
	$myaccount_page = get_page_by_title( 'My Account' );
	if ( ! empty( $myaccount_page ) ) {
		update_option( 'woocommerce_myaccount_page_id', $myaccount_page->ID );
	}

	// Update custom CSS
	$custom_css = Botiga_Custom_CSS::get_instance();
	$custom_css->update_custom_css_file();

	// Set current starter site
	atss()->current_starter( 'botiga', $demo_id );

}
add_action( 'atss_finish_import', 'botiga_setup_after_import' );

// Do not create default WooCommerce pages when plugin is activated
// The condition avoid the filter being applied in others pages
// Eg: Woo > Status > Tools > Create default pages
if ( isset( $_POST['action'] ) && $_POST['action'] === 'atss_import_plugin' ) {
	add_filter( 'woocommerce_create_pages', '__return_empty_array' );
}
