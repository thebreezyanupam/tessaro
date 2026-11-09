<?php
/**
 * Home page.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$house_stones = array( '#A8402E', '#2F6B52', '#D9A63F', '#E0A999', '#1B1A1C', '#F1EEE6' );

$hero_floor = array(
	'binder'   => '#EAE2D0',
	'stones'   => $house_stones,
	'seed'     => 31,
	'size'     => 66,
	'density'  => 0.85,
	'letters'  => array( 'TESSARO', '& DAUGHTERS' ),
	'lettersTall' => array( 'TESSARO', '&', 'DAUGHTERS' ),
	'inlay'    => '#6E2318',
	'fit'      => 0.88,
	'live'     => true,
);

$process_floor = array(
	'binder'  => '#EAE2D0',
	'stones'  => array( '#A8402E', '#1B1A1C', '#D9A63F', '#F1EEE6', '#2F6B52' ),
	'seed'    => 7,
	'size'    => 74,
	'density' => 0.95,
	'live'    => true,
);

$floors = get_posts(
	array(
		'post_type'      => 'td_project',
		'posts_per_page' => 6,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);

$stones   = td_stones();
$binders  = td_binders();
$enquiry  = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : '';
?>

<section class="hero" data-hero>
	<canvas class="slab hero__slab" data-slab="<?php echo td_data( $hero_floor ); ?>" aria-hidden="true"></canvas>
	<h1 class="hero__title">Tessaro &amp; Daughters</h1>

	<div class="stickers">
		<div class="sticker sticker--stamp" data-sticker data-tilt="-9" data-at="1.1" data-speed="150">
			<div class="sticker__drag"><div class="sticker__face">
				<div class="sticker__paper">
					<svg viewBox="0 0 200 200" role="img" aria-label="<?php echo esc_attr( td( 'st_stamp_mid' ) . ', ' . td( 'st_stamp_ring' ) ); ?>">
						<defs><path id="td-stamp-ring" d="M100 100m-76 0a76 76 0 1 1 152 0a76 76 0 1 1-152 0"/></defs>
						<text class="stamp__ring"><textPath href="#td-stamp-ring" textLength="470"><?php echo esc_html( td( 'st_stamp_ring' ) ); ?></textPath></text>
						<text class="stamp__mid" x="100" y="120" text-anchor="middle"><?php echo esc_html( td( 'st_stamp_mid' ) ); ?></text>
					</svg>
				</div>
			</div></div>
		</div>

		<div class="sticker sticker--sons" data-sticker data-tilt="4" data-at="1.5" data-speed="90">
			<div class="sticker__drag"><div class="sticker__face">
				<p class="sticker__paper"><?php echo esc_html( td( 'st_sons' ) ); ?></p>
			</div></div>
		</div>

		<div class="sticker sticker--wax" data-sticker data-tilt="-5" data-at="2.6" data-speed="70">
			<div class="sticker__drag"><div class="sticker__face">
				<p class="sticker__paper"><span><?php echo esc_html( td( 'st_wax' ) ); ?></span></p>
			</div></div>
		</div>

		<div class="sticker sticker--cta" data-sticker data-tilt="3" data-at="3" data-speed="160">
			<div class="sticker__drag"><div class="sticker__face">
				<a class="sticker__paper" href="#yard"><?php echo esc_html( td( 'st_cta' ) ); ?> <span aria-hidden="true">&rarr;</span></a>
			</div></div>
		</div>
	</div>

	<div class="ticker" aria-hidden="true">
		<div class="ticker__run">
			<?php for ( $run = 0; $run < 4; $run++ ) : ?>
				<span class="ticker__set">
					<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
						<span><?php echo esc_html( td( "s{$i}_title" ) ); ?></span><i class="ticker__chip" style="--swatch:<?php echo esc_attr( $house_stones[ $i % 4 ] ); ?>"></i>
					<?php endfor; ?>
				</span>
			<?php endfor; ?>
		</div>
	</div>

	<div class="hero__foot">
		<p class="hero__kicker"><?php echo esc_html( td( 'hero_kicker' ) ); ?></p>
		<p class="hero__hint" data-hero-hint><?php echo esc_html( td( 'hero_hint' ) ); ?></p>
	</div>
</section>

<section class="say" data-say>
	<div class="say__chips" aria-hidden="true">
		<?php
		// Loose stone scattered round the statement: x%, y%, size, drift, colour.
		$loose = array(
			array( 7, 6, 58, 150, '#A8402E' ),
			array( 19, 11, 22, 260, '#1B1A1C' ),
			array( 33, 5, 34, 90, '#D9A63F' ),
			array( 52, 12, 16, 300, '#2F6B52' ),
			array( 64, 6, 44, 170, '#E0A999' ),
			array( 93, 91, 26, 120, '#6FA8A0' ),
			array( 4, 88, 40, 210, '#2F6B52' ),
			array( 17, 94, 18, 110, '#A8402E' ),
			array( 36, 87, 62, 240, '#D9A63F' ),
			array( 50, 93, 24, 130, '#1B1A1C' ),
		);
		foreach ( $loose as $chip ) {
			printf(
				'<i class="say__chip" data-chip="%1$d" style="--x:%2$d%%;--y:%3$d%%;--s:%4$dpx;--swatch:%5$s"></i>',
				(int) $chip[3],
				(int) $chip[0],
				(int) $chip[1],
				(int) $chip[2],
				esc_attr( $chip[4] )
			);
		}
		?>
	</div>
	<p class="say__text" data-words><?php echo esc_html( td( 'manifesto' ) ); ?></p>
	<div class="stickers">
		<div class="sticker sticker--lane" data-sticker data-on-scroll data-tilt="-4" data-speed="110">
			<div class="sticker__drag"><div class="sticker__face">
				<p class="sticker__paper"><?php echo esc_html( td( 'st_lane' ) ); ?></p>
			</div></div>
		</div>
		<div class="sticker sticker--tag" data-sticker data-on-scroll data-tilt="6" data-speed="70">
			<div class="sticker__drag"><div class="sticker__face">
				<p class="sticker__paper" style="--swatch:<?php echo esc_attr( $stones[0]['hex'] ?? '#A8402E' ); ?>">
					<span class="chip__shape" aria-hidden="true"></span>
					<span class="tag__text"><i><?php echo esc_html( td( 'st_tag' ) ); ?></i><b><?php echo esc_html( $stones[0]['name'] ?? 'Verona Red' ); ?></b></span>
				</p>
			</div></div>
		</div>
	</div>
</section>

<section class="process" id="process" data-process>
	<canvas class="slab process__slab" data-slab="<?php echo td_data( $process_floor ); ?>" aria-hidden="true"></canvas>
	<div class="process__panel">
		<h2 class="process__label"><?php esc_html_e( 'How a floor gets made', 'tessaro' ); ?></h2>
		<ol class="process__steps">
			<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
				<li class="step" data-step>
					<span class="step__n" aria-hidden="true"><?php echo esc_html( $i ); ?>/4</span>
					<h3 class="step__name"><?php echo esc_html( td( "p{$i}_title" ) ); ?></h3>
					<p class="step__text"><?php echo esc_html( td( "p{$i}_text" ) ); ?></p>
				</li>
			<?php endfor; ?>
		</ol>
		<div class="process__bar" aria-hidden="true"><i data-process-bar></i></div>
	</div>
</section>

<section class="work" id="services">
	<h2 class="work__title" data-stretch><?php esc_html_e( 'What we do', 'tessaro' ); ?></h2>
	<ul class="work__list">
		<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
			<li class="work__row" data-rise>
				<h3><?php echo esc_html( td( "s{$i}_title" ) ); ?></h3>
				<p><?php echo esc_html( td( "s{$i}_text" ) ); ?></p>
				<span><?php echo esc_html( td( "s{$i}_price" ) ); ?></span>
			</li>
		<?php endfor; ?>
	</ul>
</section>

<section class="mix" id="mix" data-mix>
	<header class="mix__head">
		<h2 class="mix__title" data-stretch><?php esc_html_e( 'Mix your own', 'tessaro' ); ?></h2>
		<p class="mix__lede"><?php esc_html_e( 'Pick a binder and some stone to see how a floor might look. If you like it, send it to us and we will make you a real sample.', 'tessaro' ); ?></p>
	</header>
	<div class="mix__table">
		<div class="mix__stage">
			<canvas class="slab mix__slab" data-mix-slab aria-label="<?php esc_attr_e( 'Preview of your terrazzo mix', 'tessaro' ); ?>" role="img"></canvas>
			<output class="mix__code" data-mix-code>TD-0000</output>
		</div>
		<div class="mix__controls">
			<fieldset class="mix__group">
				<legend><?php esc_html_e( 'Binder', 'tessaro' ); ?></legend>
				<div class="mix__binders">
					<?php foreach ( $binders as $name => $hex ) : ?>
						<button type="button" class="binder" data-binder="<?php echo esc_attr( $hex ); ?>" aria-pressed="<?php echo 'Bone' === $name ? 'true' : 'false'; ?>" style="--swatch:<?php echo esc_attr( $hex ); ?>">
							<span class="binder__swatch" aria-hidden="true"></span><?php echo esc_html( $name ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			</fieldset>
			<fieldset class="mix__group">
				<legend><?php esc_html_e( 'Stone, up to six', 'tessaro' ); ?></legend>
				<div class="mix__stones">
					<?php foreach ( $stones as $index => $stone ) : ?>
						<button type="button" class="chip" data-stone="<?php echo esc_attr( $stone['hex'] ); ?>" aria-pressed="<?php echo $index < 4 ? 'true' : 'false'; ?>" style="--swatch:<?php echo esc_attr( $stone['hex'] ); ?>">
							<span class="chip__shape" aria-hidden="true"></span><?php echo esc_html( $stone['name'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			</fieldset>
			<div class="mix__sliders">
				<label><span><?php esc_html_e( 'Chip size', 'tessaro' ); ?></span>
					<input type="range" min="20" max="130" step="1" value="62" data-mix-size>
				</label>
				<label><span><?php esc_html_e( 'How much stone', 'tessaro' ); ?></span>
					<input type="range" min="0.45" max="1.3" step="0.01" value="0.95" data-mix-density>
				</label>
			</div>
			<div class="mix__actions">
				<button type="button" class="btn btn--ghost" data-mix-shuffle><?php esc_html_e( 'Try another pattern', 'tessaro' ); ?></button>
				<a class="btn" href="#yard" data-mix-send><?php esc_html_e( 'Send us this mix', 'tessaro' ); ?></a>
			</div>
		</div>
	</div>
</section>

<section class="floors" id="floors">
	<h2 class="floors__title" data-stretch><?php esc_html_e( 'Floors', 'tessaro' ); ?></h2>
	<div class="floors__grid">
		<?php
		foreach ( $floors as $index => $floor ) :
			$mix = td_project_mix( $floor->ID );
			?>
			<a class="card card--<?php echo esc_attr( $index % 6 ); ?>" href="<?php echo esc_url( get_permalink( $floor ) ); ?>">
				<span class="card__floor">
					<?php if ( has_post_thumbnail( $floor ) ) : ?>
						<?php echo get_the_post_thumbnail( $floor, 'large', array( 'class' => 'card__photo' ) ); ?>
					<?php else : ?>
						<canvas data-bake="<?php echo td_data( $mix ); ?>" aria-hidden="true"></canvas>
					<?php endif; ?>
				</span>
				<span class="card__meta">
					<span class="card__name"><?php echo esc_html( get_the_title( $floor ) ); ?></span>
					<span class="card__facts"><?php echo esc_html( get_post_meta( $floor->ID, '_td_work', true ) . ', ' . get_post_meta( $floor->ID, '_td_area', true ) . ', ' . get_post_meta( $floor->ID, '_td_year', true ) ); ?></span>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
</section>

<section class="stones" id="stones">
	<h2 class="stones__title"><?php esc_html_e( 'Stone we keep in stock', 'tessaro' ); ?></h2>
	<ul class="stones__list">
		<?php foreach ( $stones as $stone ) : ?>
			<li class="stone" style="--swatch:<?php echo esc_attr( $stone['hex'] ); ?>;--on:<?php echo esc_attr( td_on_color( $stone['hex'] ) ); ?>">
				<span class="stone__name"><?php echo esc_html( $stone['name'] ); ?></span>
				<span class="stone__from"><?php echo esc_html( $stone['origin'] ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</section>

<section class="family" id="family">
	<h2 class="family__title"><?php esc_html_e( 'About the "& Daughters"', 'tessaro' ); ?></h2>
	<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
		<article class="era" data-rise>
			<span class="era__year"><?php echo esc_html( td( "h{$i}_year" ) ); ?></span>
			<p class="era__text"><?php echo esc_html( td( "h{$i}_text" ) ); ?></p>
		</article>
	<?php endfor; ?>
</section>

<section class="yard" id="yard">
	<div class="yard__intro">
		<h2 class="yard__title"><?php esc_html_e( 'Tell us about the floor', 'tessaro' ); ?></h2>
		<p class="yard__lede"><?php echo esc_html( td( 'visit' ) ); ?></p>
		<dl class="yard__facts">
			<dt><?php esc_html_e( 'Find us', 'tessaro' ); ?></dt><dd><?php echo esc_html( td( 'address' ) ); ?></dd>
			<dt><?php esc_html_e( 'Visit', 'tessaro' ); ?></dt><dd><?php echo esc_html( td( 'hours' ) ); ?></dd>
			<dt><?php esc_html_e( 'Call', 'tessaro' ); ?></dt><dd><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', td( 'phone' ) ) ); ?>"><?php echo esc_html( td( 'phone' ) ); ?></a></dd>
		</dl>
	</div>

	<form class="form" data-endpoint="<?php echo esc_url( rest_url( 'tessaro/v1/enquiry' ) ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-enquiry>
		<input type="hidden" name="action" value="td_enquiry">
		<input type="hidden" name="mix" value="" data-form-mix>
		<?php wp_nonce_field( 'td_enquiry', 'td_nonce' ); ?>

		<div class="form__mix" data-form-mix-card hidden>
			<canvas width="96" height="96" data-form-mix-preview aria-hidden="true"></canvas>
			<p><?php esc_html_e( 'Your mix is attached:', 'tessaro' ); ?> <strong data-form-mix-code></strong></p>
		</div>

		<label class="field"><span><?php esc_html_e( 'Your name', 'tessaro' ); ?></span><input type="text" name="name" autocomplete="name" required></label>
		<label class="field"><span><?php esc_html_e( 'Email', 'tessaro' ); ?></span><input type="email" name="email" autocomplete="email" required></label>
		<label class="field"><span><?php esc_html_e( 'Phone (optional)', 'tessaro' ); ?></span><input type="tel" name="phone" autocomplete="tel"></label>
		<label class="field"><span><?php esc_html_e( 'Rough size (optional)', 'tessaro' ); ?></span><input type="text" name="area" placeholder="<?php esc_attr_e( 'About 300 sq ft', 'tessaro' ); ?>"></label>
		<label class="field field--full"><span><?php esc_html_e( 'What kind of work', 'tessaro' ); ?></span>
			<select name="kind">
				<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
					<option><?php echo esc_html( td( "s{$i}_title" ) ); ?></option>
				<?php endfor; ?>
				<option><?php esc_html_e( 'Not sure yet', 'tessaro' ); ?></option>
			</select>
		</label>
		<label class="field field--full"><span><?php esc_html_e( 'About the floor', 'tessaro' ); ?></span><textarea name="message" rows="4" required></textarea></label>
		<label class="field--trap" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>

		<div class="form__foot">
			<button class="btn btn--dark" type="submit"><?php esc_html_e( 'Send enquiry', 'tessaro' ); ?></button>
			<p class="form__status" role="status" data-form-status>
				<?php
				if ( 'sent' === $enquiry ) {
					esc_html_e( 'Thanks, we have it. We will call you within two working days.', 'tessaro' );
				} elseif ( 'error' === $enquiry ) {
					esc_html_e( 'That did not go through. Please call us instead.', 'tessaro' );
				}
				?>
			</p>
		</div>
	</form>
</section>

<?php
get_footer();
