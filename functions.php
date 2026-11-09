<?php
/**
 * Tessaro Terrazzo theme bootstrap.
 */

defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/fields.php';
require_once get_template_directory() . '/inc/content-types.php';
require_once get_template_directory() . '/inc/enquiries.php';
require_once get_template_directory() . '/inc/seed.php';

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		register_nav_menus( array( 'primary' => __( 'Primary navigation', 'tessaro' ) ) );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		$uri = get_template_directory_uri() . '/assets';
		$dir = get_template_directory() . '/assets';

		wp_enqueue_style( 'tessaro-site', $uri . '/css/site.css', array(), (string) filemtime( $dir . '/css/site.css' ) );

		wp_enqueue_script( 'gsap', $uri . '/vendor/gsap.min.js', array(), '3.15.1', true );
		wp_enqueue_script( 'gsap-scrolltrigger', $uri . '/vendor/ScrollTrigger.min.js', array( 'gsap' ), '3.15.1', true );
		wp_enqueue_script( 'gsap-splittext', $uri . '/vendor/SplitText.min.js', array( 'gsap' ), '3.15.1', true );
		wp_enqueue_script( 'lenis', $uri . '/vendor/lenis.min.js', array(), '1.3.27', true );

		// Order matters: core.js creates window.TD and main.js starts everything.
		$scripts = array(
			'slab'    => array(),
			'core'    => array( 'gsap', 'gsap-scrolltrigger', 'gsap-splittext', 'lenis' ),
			'floors'  => array( 'tessaro-core', 'tessaro-slab' ),
			'hero'    => array( 'tessaro-floors' ),
			'process' => array( 'tessaro-floors' ),
			'mix'     => array( 'tessaro-floors' ),
			'type'    => array( 'tessaro-core' ),
			'enquiry' => array( 'tessaro-core' ),
			'main'    => array( 'tessaro-hero', 'tessaro-process', 'tessaro-mix', 'tessaro-type', 'tessaro-enquiry' ),
		);
		foreach ( $scripts as $name => $deps ) {
			$file = "/js/{$name}.js";
			wp_enqueue_script( "tessaro-{$name}", $uri . $file, $deps, (string) filemtime( $dir . $file ), true );
		}
	}
);

add_action(
	'wp_head',
	static function () {
		$uri = get_template_directory_uri() . '/assets/fonts/anybody.woff2';
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( $uri ) );
	},
	1
);

add_action(
	'wp_head',
	static function () {
		if ( is_singular( 'td_project' ) && has_excerpt() ) {
			$description = get_the_excerpt();
		} else {
			$description = td( 'hero_kicker' );
		}
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
	},
	2
);

/**
 * The house palette of binders, used by the Mixing Table and the project editor.
 *
 * @return array<string,string> Name => hex.
 */
function td_binders() {
	return array(
		'Bone'     => '#EAE2D0',
		'Portland' => '#A9A79F',
		'Ochre'    => '#C88E2C',
		'Oxblood'  => '#6E2318',
		'Verde'    => '#1F4436',
		'Nero'     => '#171513',
	);
}

/**
 * Stones from the Stones post type.
 *
 * @return array<int,array{name:string,hex:string,origin:string}>
 */
function td_stones() {
	$posts  = get_posts(
		array(
			'post_type'      => 'td_stone',
			'posts_per_page' => 40,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);
	$stones = array();
	foreach ( $posts as $post ) {
		$hex = sanitize_hex_color( get_post_meta( $post->ID, '_td_hex', true ) );
		if ( $hex ) {
			$stones[] = array(
				'name'   => get_the_title( $post ),
				'hex'    => $hex,
				'origin' => (string) get_post_meta( $post->ID, '_td_origin', true ),
			);
		}
	}
	return $stones;
}

/**
 * The floor recipe for one project, ready for data-slab / data-bake.
 *
 * @param int $post_id Project ID.
 * @return array<string,mixed>
 */
function td_project_mix( $post_id ) {
	$stones = array_filter( array_map( 'sanitize_hex_color', (array) get_post_meta( $post_id, '_td_stones', true ) ) );
	return array(
		'binder'  => sanitize_hex_color( get_post_meta( $post_id, '_td_binder', true ) ) ?: '#EAE2D0',
		'stones'  => array_values( $stones ) ?: array( '#A8402E', '#1B1A1C', '#F1EEE6' ),
		'seed'    => (int) get_post_meta( $post_id, '_td_seed', true ) ?: $post_id,
		'size'    => (float) get_post_meta( $post_id, '_td_size', true ) ?: 58,
		'density' => (float) get_post_meta( $post_id, '_td_density', true ) ?: 1,
		'grid'    => (int) get_post_meta( $post_id, '_td_grid', true ),
	);
}

/** Encode a mix for the "start from this floor" link on project pages. */
function td_mix_query( array $mix ) {
	$hex = static function ( $value ) {
		return ltrim( $value, '#' );
	};
	return implode(
		'|',
		array(
			$hex( $mix['binder'] ),
			implode( '.', array_map( $hex, $mix['stones'] ) ),
			$mix['size'],
			$mix['density'],
			$mix['seed'],
		)
	);
}

/** JSON for a data attribute. */
function td_data( array $data ) {
	return esc_attr( wp_json_encode( $data ) );
}

/** Dark or light text for a given background colour. */
function td_on_color( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 6 !== strlen( $hex ) ) {
		return '#171513';
	}
	$lum = ( 0.299 * hexdec( substr( $hex, 0, 2 ) ) + 0.587 * hexdec( substr( $hex, 2, 2 ) ) + 0.114 * hexdec( substr( $hex, 4, 2 ) ) ) / 255;
	return $lum > 0.55 ? '#171513' : '#EFE8D9';
}

/** Primary navigation, falling back to the home page anchors until a menu is assigned. */
function td_nav() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav__list',
				'depth'          => 1,
			)
		);
		return;
	}
	$links = array(
		home_url( '/#process' ) => __( 'The work', 'tessaro' ),
		home_url( '/#mix' )     => __( 'Mix a floor', 'tessaro' ),
		home_url( '/#floors' )  => __( 'Floors', 'tessaro' ),
		home_url( '/#yard' )    => __( 'The yard', 'tessaro' ),
	);
	echo '<ul class="nav__list">';
	foreach ( $links as $url => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}
