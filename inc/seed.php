<?php
/**
 * Sample content, created once so that later edits and deletions in wp-admin stick.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function () {
		if ( get_option( 'td_seeded_v1' ) ) {
			return;
		}
		update_option( 'td_seeded_v1', 1 );
		td_seed();
		flush_rewrite_rules();
	},
	20
);

function td_seed_stones() {
	return array(
		array( 'Verona Red', '#A8402E', 'Verona, Italy' ),
		array( 'Verde Alpi', '#2F6B52', 'Valle d\'Aosta, Italy' ),
		array( 'Giallo Siena', '#D9A63F', 'Siena, Italy' ),
		array( 'Rosa Portogallo', '#E0A999', 'Alentejo, Portugal' ),
		array( 'Nero Marquina', '#1B1A1C', 'Basque Country, Spain' ),
		array( 'Bianco Carrara', '#F1EEE6', 'Carrara, Italy' ),
		array( 'Bardiglio', '#7B8288', 'Tuscany, Italy' ),
		array( 'Sea Glass', '#6FA8A0', 'Recycled bottle glass' ),
		array( 'Quahog Shell', '#CDBFD0', 'Crushed shell from the town dock' ),
	);
}

function td_seed_floors() {
	return array(
		array(
			'title'   => 'Harbor Street Post Office',
			'excerpt' => 'A 1938 lobby floor, restored after we found it under two layers of vinyl.',
			'work'    => 'Restoration',
			'where'   => 'Harbor Street',
			'year'    => '2023',
			'area'    => '2,400 sq ft',
			'binder'  => '#EAE2D0',
			'stones'  => array( '#A8402E', '#1B1A1C', '#F1EEE6', '#7B8288' ),
			'size'    => 46,
			'density' => 1.05,
			'grid'    => 420,
			'seed'    => 38,
			'body'    => array(
				'The post office lobby had been covered in vinyl tile twice, in 1971 and again in 1994. Underneath was the floor Ettore Tessaro laid in 1938. The brass strips were intact, and about forty square feet near the old radiator line needed replacing.',
				'We lifted the vinyl by hand and matched the missing section using stone from the same quarries. We ground the lobby over six nights so the counters could stay open during the day.',
			),
		),
		array(
			'title'   => 'The Okafor Kitchen',
			'excerpt' => 'A dark green binder with large chips, for a family kitchen.',
			'work'    => 'New floor',
			'where'   => 'Bellwether Hill',
			'year'    => '2024',
			'area'    => '340 sq ft',
			'binder'  => '#1F4436',
			'stones'  => array( '#F1EEE6', '#D9A63F', '#E0A999', '#6FA8A0' ),
			'size'    => 72,
			'density' => 0.9,
			'grid'    => 0,
			'seed'    => 240,
			'body'    => array(
				'The Okafors brought us a photo of a Lisbon tram seat and asked if we could match the green. We made up a few samples and settled on a dark binder with large chips, because small stone in a binder this dark can look like noise from standing height.',
				'The floor is one section with no brass strips, which the family preferred. We checked it again a year later and found no cracks.',
			),
		),
		array(
			'title'   => 'Port Alder Central School',
			'excerpt' => 'Restoring the corridors Rina Tessaro poured in 1962.',
			'work'    => 'Restoration',
			'where'   => 'Central Avenue',
			'year'    => '2021',
			'area'    => '11,000 sq ft',
			'binder'  => '#A9A79F',
			'stones'  => array( '#F1EEE6', '#1B1A1C', '#A8402E', '#D9A63F' ),
			'size'    => 34,
			'density' => 1.2,
			'grid'    => 300,
			'seed'    => 62,
			'body'    => array(
				'Rina Tessaro poured these corridors in the summer of 1962. In 2021 her daughters restored them during the school\'s summer break, keeping the original strip layout and replacing none of the floor.',
				'There is a worn dip outside the cafeteria door from sixty years of foot traffic. We offered to level it, and the school asked us to leave it.',
			),
		),
		array(
			'title'   => 'Vale Street Pharmacy',
			'excerpt' => 'A shop entrance with the name set in brass.',
			'work'    => 'Threshold',
			'where'   => 'Vale Street',
			'year'    => '2022',
			'area'    => '90 sq ft',
			'binder'  => '#6E2318',
			'stones'  => array( '#E0A999', '#F1EEE6', '#D9A63F' ),
			'size'    => 40,
			'density' => 1,
			'grid'    => 0,
			'seed'    => 91,
			'body'    => array(
				'Shops around here used to set their names into the entrance floor, and the pharmacy wanted one when they moved in. We cut the letters from brass strip and poured the entrance in an oxblood binder.',
				'At ninety square feet it was one of our smaller jobs that year. It took two weeks from cutting the letters to the final polish.',
			),
		),
		array(
			'title'   => 'Marine Library Reading Room',
			'excerpt' => 'A black binder with local shell, for a quiet reading room.',
			'work'    => 'New floor',
			'where'   => 'Water Street',
			'year'    => '2025',
			'area'    => '1,800 sq ft',
			'binder'  => '#171513',
			'stones'  => array( '#CDBFD0', '#F1EEE6', '#6FA8A0' ),
			'size'    => 28,
			'density' => 0.95,
			'grid'    => 0,
			'seed'    => 25,
			'body'    => array(
				'The library asked for something calm, so we kept the stone small and limited the mix to shell, white marble and a little sea glass. The shell came from the town dock, and we washed and crushed it at the yard over the winter.',
				'A dark polished floor shows dust quickly, so we went through the cleaning routine with the staff before we started.',
			),
		),
		array(
			'title'   => 'Lantern Theatre Foyer',
			'excerpt' => 'An ochre binder and a wide brass grid for a theatre foyer.',
			'work'    => 'New floor',
			'where'   => 'Market Square',
			'year'    => '2024',
			'area'    => '1,150 sq ft',
			'binder'  => '#C88E2C',
			'stones'  => array( '#1B1A1C', '#A8402E', '#F1EEE6' ),
			'size'    => 58,
			'density' => 1,
			'grid'    => 520,
			'seed'    => 77,
			'body'    => array(
				'We matched the ochre binder to the old plaster in the foyer, then spaced the brass grid wide enough to read from the balcony.',
				'The work had to fit between the summer season and opening night, so we ran two shifts during the grinding stage.',
			),
		),
	);
}

/**
 * Turn plain paragraphs into block editor markup.
 *
 * @param string[] $paragraphs Paragraph text.
 * @return string
 */
function td_seed_blocks( array $paragraphs ) {
	$blocks = array();
	foreach ( $paragraphs as $paragraph ) {
		$blocks[] = "<!-- wp:paragraph -->\n<p>" . $paragraph . "</p>\n<!-- /wp:paragraph -->";
	}
	return implode( "\n\n", $blocks );
}

function td_seed() {
	foreach ( td_seed_stones() as $order => $stone ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'td_stone',
				'post_status' => 'publish',
				'post_title'  => $stone[0],
				'menu_order'  => $order,
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_td_hex', $stone[1] );
			update_post_meta( $id, '_td_origin', $stone[2] );
		}
	}

	foreach ( td_seed_floors() as $order => $floor ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'td_project',
				'post_status'  => 'publish',
				'post_title'   => $floor['title'],
				'post_excerpt' => $floor['excerpt'],
				'post_content' => td_seed_blocks( $floor['body'] ),
				'menu_order'   => $order,
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}

		$meta = array(
			'_td_work'     => $floor['work'],
			'_td_location' => $floor['where'],
			'_td_year'     => $floor['year'],
			'_td_area'     => $floor['area'],
			'_td_binder'   => $floor['binder'],
			'_td_stones'   => $floor['stones'],
			'_td_size'     => $floor['size'],
			'_td_density'  => $floor['density'],
			'_td_grid'     => $floor['grid'],
			'_td_seed'     => $floor['seed'],
		);
		foreach ( $meta as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
	}
}
