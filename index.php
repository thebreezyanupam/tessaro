<?php
/**
 * Fallback template for pages, posts and archives.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="plain">
	<?php if ( have_posts() ) : ?>
		<?php if ( ! is_singular() ) : ?>
			<h1 class="plain__title"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ?: get_bloginfo( 'name' ) ); ?></h1>
		<?php endif; ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'prose' ); ?>>
				<?php if ( is_singular() ) : ?>
					<h1 class="plain__title"><?php the_title(); ?></h1>
					<?php the_content(); ?>
				<?php else : ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<h1 class="plain__title"><?php esc_html_e( 'Nothing here yet', 'tessaro' ); ?></h1>
	<?php endif; ?>
</section>
<?php
get_footer();
