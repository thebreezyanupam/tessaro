<?php
/**
 * A single floor.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$mix         = td_project_mix( get_the_ID() );
	$live        = array_merge( $mix, array( 'live' => true ) );
	$stone_names = array();
	foreach ( td_stones() as $stone ) {
		if ( in_array( strtoupper( $stone['hex'] ), array_map( 'strtoupper', $mix['stones'] ), true ) ) {
			$stone_names[] = $stone;
		}
	}
	// The next floor in the owner's order, wrapping round to the first.
	$all_floors = get_posts(
		array(
			'post_type'      => 'td_project',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'fields'         => 'ids',
		)
	);
	$position   = array_search( get_the_ID(), $all_floors, true );
	$next_id    = count( $all_floors ) > 1 ? $all_floors[ ( (int) $position + 1 ) % count( $all_floors ) ] : 0;
	?>
	<article class="floor">
		<div class="floor__hero" data-floor-hero>
			<canvas class="slab" data-slab="<?php echo td_data( $live ); ?>" aria-hidden="true"></canvas>
		</div>
		<header class="floor__head">
			<h1 class="floor__title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="floor__lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</header>
		<div class="floor__body">
			<dl class="floor__facts">
				<dt><?php esc_html_e( 'Work', 'tessaro' ); ?></dt><dd><?php echo esc_html( get_post_meta( get_the_ID(), '_td_work', true ) ); ?></dd>
				<dt><?php esc_html_e( 'Where', 'tessaro' ); ?></dt><dd><?php echo esc_html( get_post_meta( get_the_ID(), '_td_location', true ) ); ?></dd>
				<dt><?php esc_html_e( 'Year', 'tessaro' ); ?></dt><dd><?php echo esc_html( get_post_meta( get_the_ID(), '_td_year', true ) ); ?></dd>
				<dt><?php esc_html_e( 'Area', 'tessaro' ); ?></dt><dd><?php echo esc_html( get_post_meta( get_the_ID(), '_td_area', true ) ); ?></dd>
				<?php if ( $stone_names ) : ?>
					<dt><?php esc_html_e( 'Stone', 'tessaro' ); ?></dt>
					<dd class="floor__stones">
						<?php foreach ( $stone_names as $stone ) : ?>
							<span style="--swatch:<?php echo esc_attr( $stone['hex'] ); ?>"><span class="chip__shape" aria-hidden="true"></span><?php echo esc_html( $stone['name'] ); ?></span>
						<?php endforeach; ?>
					</dd>
				<?php endif; ?>
			</dl>
			<div class="prose">
				<?php the_content(); ?>
				<p class="floor__cta"><a class="btn btn--dark" href="<?php echo esc_url( add_query_arg( 'mix', td_mix_query( $mix ), home_url( '/' ) ) . '#mix' ); ?>"><?php esc_html_e( 'Start a mix from this floor', 'tessaro' ); ?></a></p>
			</div>
		</div>
		<?php if ( $next_id ) : ?>
			<a class="floor__next" href="<?php echo esc_url( get_permalink( $next_id ) ); ?>">
				<span><?php esc_html_e( 'Next floor', 'tessaro' ); ?></span>
				<strong><?php echo esc_html( get_the_title( $next_id ) ); ?></strong>
			</a>
		<?php endif; ?>
	</article>
	<?php
endwhile;

get_footer();
