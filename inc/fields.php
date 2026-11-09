<?php
/**
 * Home page copy, editable under Appearance > Customize > Tessaro & Daughters.
 *
 * Each field is: key => array( label, default text, control type ).
 */

defined( 'ABSPATH' ) || exit;

function td_field_groups() {
	return array(
		'td_hero'     => array(
			'title'  => 'Opening floor',
			'fields' => array(
				'hero_kicker' => array( 'Line under the floor', 'We pour, grind and restore terrazzo floors in Port Alder. Family business since 1931.', 'textarea' ),
				'hero_hint'   => array( 'Interaction hint', 'These stickers come off. Try one.' ),
			),
		),
		'td_stickers' => array(
			'title'  => 'Opening floor stickers',
			'fields' => array(
				'st_stamp_mid'  => array( 'Round stamp, centre', '1931' ),
				'st_stamp_ring' => array( 'Round stamp, ring', 'Tessaro & Daughters * Terrazzo Works * ' ),
				'st_sons'       => array( 'Green sticker', '3 generations. 0 sons.' ),
				'st_lane'       => array( 'Paper sticker', 'Ground by hand on Foundry Lane' ),
				'st_tag'        => array( 'Sample tag label (shows your first stone)', 'Sample no. 1' ),
				'st_wax'        => array( 'Black stone sticker', 'No wax. No coating.' ),
				'st_cta'        => array( 'Button sticker', 'Free floor check' ),
			),
		),
		'td_story'    => array(
			'title'  => 'Opening statement',
			'fields' => array(
				'manifesto' => array( 'Statement', 'Terrazzo is marble and glass chips set in a binder, then ground flat until the stone shows. We have worked from the same yard on Foundry Lane since 1931, and many of our old floors are still in daily use.', 'textarea' ),
			),
		),
		'td_process'  => array(
			'title'  => 'The four stages',
			'fields' => array(
				'p1_title' => array( 'Stage 1 name', 'Pour' ),
				'p1_text'  => array( 'Stage 1 text', 'We mix marble, glass and shell into wet binder, then spread and roll it between brass strips, one section at a time.', 'textarea' ),
				'p2_title' => array( 'Stage 2 name', 'Cure' ),
				'p2_text'  => array( 'Stage 2 text', 'The floor has to harden before we can work on it. That takes at least seven days, and longer if the building is cold.', 'textarea' ),
				'p3_title' => array( 'Stage 3 name', 'Grind' ),
				'p3_text'  => array( 'Stage 3 text', 'We make four passes, from coarse to fine. This is when the stone first shows, so it is also when you see the real colours.', 'textarea' ),
				'p4_title' => array( 'Stage 4 name', 'Polish' ),
				'p4_text'  => array( 'Stage 4 text', 'We finish at 3000 grit and seal the surface. There is no wax or topcoat to peel, and upkeep is mostly a damp mop.', 'textarea' ),
			),
		),
		'td_services' => array(
			'title'  => 'What we do',
			'fields' => array(
				's1_title' => array( 'Service 1', 'New floors' ),
				's1_text'  => array( 'Service 1 text', 'Poured on site. We have done single entry halls and whole school corridors.', 'textarea' ),
				's1_price' => array( 'Service 1 price', 'from $48 / sq ft' ),
				's2_title' => array( 'Service 2', 'Restoration' ),
				's2_text'  => array( 'Service 2 text', 'Old terrazzo under carpet or linoleum is often in good shape. We will come and look at yours for free.', 'textarea' ),
				's2_price' => array( 'Service 2 price', 'from $14 / sq ft' ),
				's3_title' => array( 'Service 3', 'Stairs and thresholds' ),
				's3_text'  => array( 'Service 3 text', 'Precast stair treads, and shop entrances with your name set in brass.', 'textarea' ),
				's3_price' => array( 'Service 3 price', 'from $310 / tread' ),
				's4_title' => array( 'Service 4', 'Repairs' ),
				's4_text'  => array( 'Service 4 text', 'Cracks, missing chips and old patch jobs. Send us a photo and we can usually match the original mix.', 'textarea' ),
				's4_price' => array( 'Service 4 price', 'from $220' ),
				's5_title' => array( 'Service 5', 'Counters and tabletops' ),
				's5_text'  => array( 'Service 5 text', 'Cast and finished in our yard, then delivered and fitted by us.', 'textarea' ),
				's5_price' => array( 'Service 5 price', 'from $120 / sq ft' ),
			),
		),
		'td_history'  => array(
			'title'  => 'The family',
			'fields' => array(
				'h1_year' => array( 'Chapter 1 year', '1931' ),
				'h1_text' => array( 'Chapter 1 text', 'Ettore Tessaro came over from Friuli, rented a shed on Foundry Lane and started laying floors. He painted "Tessaro & Sons" on the door before he had any children.', 'textarea' ),
				'h2_year' => array( 'Chapter 2 year', '1962' ),
				'h2_text' => array( 'Chapter 2 text', 'His only child, Rina, was running the business by then. That summer she poured 11,000 square feet of corridor at Port Alder Central School.', 'textarea' ),
				'h3_year' => array( 'Chapter 3 year', '2011' ),
				'h3_text' => array( 'Chapter 3 text', 'Rina\'s daughters, Giulia and Marta, took over the yard and finally repainted the door.', 'textarea' ),
			),
		),
		'td_contact'  => array(
			'title'  => 'The yard',
			'fields' => array(
				'address' => array( 'Address', '41 Foundry Lane, Port Alder' ),
				'phone'   => array( 'Phone', '(401) 555-0142' ),
				'email'   => array( 'Email', 'yard@tessaro.example' ),
				'hours'   => array( 'Opening hours', 'Sample wall open Tuesday to Saturday, 8 to 4.' ),
				'visit'   => array( 'Enquiry intro', 'Tell us what you have in mind. Giulia or Marta will call you back within two working days to talk it through.', 'textarea' ),
			),
		),
	);
}

function td( $key ) {
	static $defaults = null;
	if ( null === $defaults ) {
		$defaults = array();
		foreach ( td_field_groups() as $group ) {
			foreach ( $group['fields'] as $id => $field ) {
				$defaults[ $id ] = $field[1];
			}
		}
	}
	return (string) get_theme_mod( 'td_' . $key, $defaults[ $key ] ?? '' );
}

add_action(
	'customize_register',
	static function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_panel( 'td_panel', array( 'title' => __( 'Tessaro & Daughters', 'tessaro' ) ) );

		foreach ( td_field_groups() as $section => $group ) {
			$wp_customize->add_section(
				$section,
				array(
					'title' => $group['title'],
					'panel' => 'td_panel',
				)
			);

			foreach ( $group['fields'] as $id => $field ) {
				$wp_customize->add_setting(
					'td_' . $id,
					array(
						'default'           => $field[1],
						'sanitize_callback' => 'sanitize_textarea_field',
					)
				);
				$wp_customize->add_control(
					'td_' . $id,
					array(
						'label'   => $field[0],
						'section' => $section,
						'type'    => $field[2] ?? 'text',
					)
				);
			}
		}
	}
);
