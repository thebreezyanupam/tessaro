<?php
/**
 * Stones and Floors (projects). Each floor stores a recipe that the front end
 * renders live, so a project looks right before anyone has photographed it.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function () {
		register_post_type(
			'td_project',
			array(
				'labels'       => array(
					'name'          => __( 'Floors', 'tessaro' ),
					'singular_name' => __( 'Floor', 'tessaro' ),
					'add_new_item'  => __( 'Add floor', 'tessaro' ),
					'edit_item'     => __( 'Edit floor', 'tessaro' ),
				),
				'public'       => true,
				'has_archive'  => 'floors',
				'rewrite'      => array( 'slug' => 'floors' ),
				'menu_icon'    => 'dashicons-grid-view',
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			)
		);

		register_post_type(
			'td_stone',
			array(
				'labels'       => array(
					'name'          => __( 'Stones', 'tessaro' ),
					'singular_name' => __( 'Stone', 'tessaro' ),
					'add_new_item'  => __( 'Add stone', 'tessaro' ),
					'edit_item'     => __( 'Edit stone', 'tessaro' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'menu_icon'    => 'dashicons-marker',
				'supports'     => array( 'title', 'page-attributes' ),
			)
		);
	}
);

/** The Floors listing follows the owner's order, matching the home page. */
add_action(
	'pre_get_posts',
	static function ( WP_Query $query ) {
		if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'td_project' ) ) {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}
	}
);

add_action(
	'add_meta_boxes',
	static function () {
		add_meta_box( 'td_project_box', __( 'Floor recipe and facts', 'tessaro' ), 'td_project_box', 'td_project', 'normal', 'high' );
		add_meta_box( 'td_stone_box', __( 'Stone details', 'tessaro' ), 'td_stone_box', 'td_stone', 'normal', 'high' );
	}
);

/** Project recipe editor. */
function td_project_box( WP_Post $post ) {
	wp_nonce_field( 'td_save_meta', 'td_meta_nonce' );
	$mix     = td_project_mix( $post->ID );
	$field   = static function ( $label, $name, $value, $type = 'text', $attrs = '' ) {
		printf(
			'<p><label style="display:block;font-weight:600;margin-bottom:4px">%1$s</label><input type="%2$s" name="%3$s" value="%4$s" class="regular-text" %5$s></p>',
			esc_html( $label ),
			esc_attr( $type ),
			esc_attr( $name ),
			esc_attr( $value ),
			$attrs
		);
	};
	$field( __( 'Location', 'tessaro' ), 'td_location', get_post_meta( $post->ID, '_td_location', true ) );
	$field( __( 'Year', 'tessaro' ), 'td_year', get_post_meta( $post->ID, '_td_year', true ) );
	$field( __( 'Area', 'tessaro' ), 'td_area', get_post_meta( $post->ID, '_td_area', true ) );
	$field( __( 'Type of work', 'tessaro' ), 'td_work', get_post_meta( $post->ID, '_td_work', true ) );

	echo '<hr><p style="font-weight:600">' . esc_html__( 'Binder', 'tessaro' ) . '</p><p>';
	foreach ( td_binders() as $name => $hex ) {
		printf(
			'<label style="margin-right:14px"><input type="radio" name="td_binder" value="%1$s" %2$s> <span style="display:inline-block;width:14px;height:14px;background:%1$s;border:1px solid #999;vertical-align:-2px"></span> %3$s</label>',
			esc_attr( $hex ),
			checked( strtoupper( $mix['binder'] ), strtoupper( $hex ), false ),
			esc_html( $name )
		);
	}
	echo '</p><p style="font-weight:600">' . esc_html__( 'Stones', 'tessaro' ) . '</p><p>';
	$chosen = array_map( 'strtoupper', $mix['stones'] );
	foreach ( td_stones() as $stone ) {
		printf(
			'<label style="display:inline-block;margin:0 14px 6px 0"><input type="checkbox" name="td_stones[]" value="%1$s" %2$s> <span style="display:inline-block;width:14px;height:14px;background:%1$s;border:1px solid #999;vertical-align:-2px"></span> %3$s</label>',
			esc_attr( $stone['hex'] ),
			checked( in_array( strtoupper( $stone['hex'] ), $chosen, true ), true, false ),
			esc_html( $stone['name'] )
		);
	}
	echo '</p>';
	$field( __( 'Chip size (px, 20 to 140)', 'tessaro' ), 'td_size', $mix['size'], 'number', 'min="20" max="140"' );
	$field( __( 'Chip density (0.4 to 1.3)', 'tessaro' ), 'td_density', $mix['density'], 'number', 'min="0.4" max="1.3" step="0.05"' );
	$field( __( 'Brass strip spacing (px, 0 for none)', 'tessaro' ), 'td_grid', $mix['grid'], 'number', 'min="0" max="800"' );
	$field( __( 'Pattern seed', 'tessaro' ), 'td_seed', $mix['seed'], 'number' );
	echo '<p class="description">' . esc_html__( 'Set a featured image to show a photo in place of the generated floor on listing pages.', 'tessaro' ) . '</p>';
}

/** Stone editor. */
function td_stone_box( WP_Post $post ) {
	wp_nonce_field( 'td_save_meta', 'td_meta_nonce' );
	printf(
		'<p><label>%1$s <input type="color" name="td_hex" value="%2$s"></label></p><p><label>%3$s <input type="text" class="regular-text" name="td_origin" value="%4$s"></label></p>',
		esc_html__( 'Colour', 'tessaro' ),
		esc_attr( get_post_meta( $post->ID, '_td_hex', true ) ?: '#A8402E' ),
		esc_html__( 'Where it comes from', 'tessaro' ),
		esc_attr( get_post_meta( $post->ID, '_td_origin', true ) )
	);
}

add_action(
	'save_post',
	static function ( $post_id ) {
		if ( ! isset( $_POST['td_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['td_meta_nonce'] ), 'td_save_meta' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$type = get_post_type( $post_id );

		if ( 'td_stone' === $type ) {
			update_post_meta( $post_id, '_td_hex', sanitize_hex_color( wp_unslash( $_POST['td_hex'] ?? '' ) ) );
			update_post_meta( $post_id, '_td_origin', sanitize_text_field( wp_unslash( $_POST['td_origin'] ?? '' ) ) );
		}

		if ( 'td_project' === $type ) {
			foreach ( array( 'location', 'year', 'area', 'work' ) as $key ) {
				update_post_meta( $post_id, '_td_' . $key, sanitize_text_field( wp_unslash( $_POST[ 'td_' . $key ] ?? '' ) ) );
			}
			$stones = array_map( 'sanitize_hex_color', array_map( 'wp_unslash', (array) ( $_POST['td_stones'] ?? array() ) ) );
			update_post_meta( $post_id, '_td_stones', array_slice( array_values( array_filter( $stones ) ), 0, 8 ) );
			update_post_meta( $post_id, '_td_binder', sanitize_hex_color( wp_unslash( $_POST['td_binder'] ?? '' ) ) );
			update_post_meta( $post_id, '_td_size', min( 140, max( 20, (float) ( $_POST['td_size'] ?? 58 ) ) ) );
			update_post_meta( $post_id, '_td_density', min( 1.3, max( 0.4, (float) ( $_POST['td_density'] ?? 1 ) ) ) );
			update_post_meta( $post_id, '_td_grid', min( 800, max( 0, (int) ( $_POST['td_grid'] ?? 0 ) ) ) );
			update_post_meta( $post_id, '_td_seed', (int) ( $_POST['td_seed'] ?? $post_id ) );
		}
	}
);
