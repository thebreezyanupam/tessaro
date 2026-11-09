<?php
/**
 * All floors.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="floors floors--archive">
	<h1 class="floors__title" data-stretch><?php esc_html_e( 'Floors', 'tessaro' ); ?></h1>
	<div class="floors__grid">
		<?php
		$index = 0;
		while ( have_posts() ) :
			the_post();
			$mix = td_project_mix( get_the_ID() );
			?>
			<a class="card card--<?php echo esc_attr( $index++ % 6 ); ?>" href="<?php the_permalink(); ?>">
				<span class="card__floor">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', array( 'class' => 'card__photo' ) ); ?>
					<?php else : ?>
						<canvas data-bake="<?php echo td_data( $mix ); ?>" aria-hidden="true"></canvas>
					<?php endif; ?>
				</span>
				<span class="card__meta">
					<span class="card__name"><?php the_title(); ?></span>
					<span class="card__facts"><?php echo esc_html( get_post_meta( get_the_ID(), '_td_work', true ) . ', ' . get_post_meta( get_the_ID(), '_td_area', true ) . ', ' . get_post_meta( get_the_ID(), '_td_year', true ) ); ?></span>
				</span>
			</a>
		<?php endwhile; ?>
	</div>
	<?php the_posts_pagination(); ?>
</section>
<?php
get_footer();
