<?php
/**
 * Site header.
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main"><?php esc_html_e( 'Skip to content', 'tessaro' ); ?></a>
<header class="bar" data-bar>
	<a class="bar__mark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Tessaro & Daughters, home', 'tessaro' ); ?>">Tessaro <i>&amp;</i> Daughters</a>
	<button class="bar__toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle><?php esc_html_e( 'Menu', 'tessaro' ); ?></button>
	<nav class="nav" id="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'tessaro' ); ?>" data-nav>
		<?php td_nav(); ?>
		<a class="nav__call" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', td( 'phone' ) ) ); ?>"><?php echo esc_html( td( 'phone' ) ); ?></a>
	</nav>
</header>
<main id="main">
