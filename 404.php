<?php
/**
 * Page not found.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$lost_floor = array(
	'binder'  => '#EAE2D0',
	'stones'  => array( '#A8402E', '#1B1A1C', '#D9A63F', '#2F6B52' ),
	'seed'    => 404,
	'size'    => 60,
	'density' => 0.8,
	'letters' => array( '404' ),
	'inlay'   => '#1F4436',
	'fit'     => 0.6,
	'live'    => true,
);
?>
<section class="lost">
	<canvas class="slab" data-slab="<?php echo td_data( $lost_floor ); ?>" aria-hidden="true"></canvas>
	<div class="lost__note">
		<h1><?php esc_html_e( 'Page not found', 'tessaro' ); ?></h1>
		<p><a class="btn btn--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the home page', 'tessaro' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
