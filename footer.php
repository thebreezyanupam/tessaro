<?php
/**
 * Site footer: one last floor to walk on, then the practical details.
 */

defined( 'ABSPATH' ) || exit;

$footer_floor = array(
	'binder'   => '#171513',
	'stones'   => array( '#A8402E', '#D9A63F', '#E0A999', '#F1EEE6', '#6FA8A0', '#2F6B52' ),
	'seed'     => 19,
	'size'     => 60,
	'density'  => 1,
	'letters'  => array( 'COME WALK', 'ON IT' ),
	'inlay'    => '#EAE2D0',
	'fit'      => 0.86,
	'live'     => true,
);
?>
</main>
<footer class="foot">
	<a class="foot__floor" href="<?php echo esc_url( home_url( '/#yard' ) ); ?>" aria-label="<?php esc_attr_e( 'Come walk on it. Visit the yard.', 'tessaro' ); ?>">
		<canvas class="slab" data-slab="<?php echo td_data( $footer_floor ); ?>" aria-hidden="true"></canvas>
	</a>
	<div class="foot__grid">
		<p class="foot__name">Tessaro <i>&amp;</i> Daughters<br><span><?php esc_html_e( 'Terrazzo works, est. 1931', 'tessaro' ); ?></span></p>
		<p><?php echo esc_html( td( 'address' ) ); ?><br><?php echo esc_html( td( 'hours' ) ); ?></p>
		<p>
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', td( 'phone' ) ) ); ?>"><?php echo esc_html( td( 'phone' ) ); ?></a><br>
			<a href="mailto:<?php echo esc_attr( td( 'email' ) ); ?>"><?php echo esc_html( td( 'email' ) ); ?></a>
		</p>
		<p class="foot__note"><?php esc_html_e( 'Tessaro & Daughters and Port Alder are fictional. The contents and writings are AI-generated. Custom made by Anupam Pandey. Every floor on this site is drawn live by your browser.', 'tessaro' ); ?></p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
