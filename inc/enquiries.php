<?php
/**
 * Enquiries: saved privately in wp-admin and emailed to the site admin.
 * The form posts over REST, with admin-post.php as the no-JavaScript fallback.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function () {
		register_post_type(
			'td_enquiry',
			array(
				'labels'       => array(
					'name'          => __( 'Enquiries', 'tessaro' ),
					'singular_name' => __( 'Enquiry', 'tessaro' ),
					'edit_item'     => __( 'Enquiry', 'tessaro' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => false,
				'menu_icon'    => 'dashicons-email-alt',
				'supports'     => array( 'title' ),
				'capabilities' => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap' => true,
			)
		);
	}
);

/**
 * Validate and store an enquiry.
 *
 * @param array<string,mixed> $input Raw request fields.
 * @return true|WP_Error
 */
function td_save_enquiry( array $input ) {
	// Bots fill the hidden "website" field.
	if ( ! empty( $input['website'] ) ) {
		return true;
	}

	$ip_key = 'td_enq_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= 5 ) {
		return new WP_Error( 'td_rate', __( 'Too many enquiries from this connection. Please call us instead.', 'tessaro' ), array( 'status' => 429 ) );
	}

	$name    = sanitize_text_field( (string) ( $input['name'] ?? '' ) );
	$email   = sanitize_email( (string) ( $input['email'] ?? '' ) );
	$phone   = sanitize_text_field( (string) ( $input['phone'] ?? '' ) );
	$kind    = sanitize_text_field( (string) ( $input['kind'] ?? '' ) );
	$area    = sanitize_text_field( (string) ( $input['area'] ?? '' ) );
	$message = sanitize_textarea_field( (string) ( $input['message'] ?? '' ) );
	$mix     = sanitize_text_field( (string) ( $input['mix'] ?? '' ) );

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		return new WP_Error( 'td_invalid', __( 'Please add your name, a valid email address and a short message.', 'tessaro' ), array( 'status' => 422 ) );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'td_enquiry',
			'post_status' => 'private',
			'post_title'  => wp_strip_all_tags( $name . ( $kind ? ' / ' . $kind : '' ) ),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		return new WP_Error( 'td_save', __( 'We could not save your enquiry. Please call us instead.', 'tessaro' ), array( 'status' => 500 ) );
	}

	foreach ( compact( 'name', 'email', 'phone', 'kind', 'area', 'message', 'mix' ) as $key => $value ) {
		update_post_meta( $post_id, '_td_' . $key, $value );
	}

	set_transient( $ip_key, $count + 1, HOUR_IN_SECONDS );

	// Mail is sent at the end of this request rather than through WP-Cron, which depends on
	// loopback requests and later traffic. A missed enquiry email costs the business a job.
	add_action(
		'shutdown',
		static function () use ( $post_id ) {
			// Where the server allows it, answer the visitor first so a slow mail server can't delay them.
			if ( function_exists( 'fastcgi_finish_request' ) ) {
				fastcgi_finish_request();
			} elseif ( function_exists( 'litespeed_finish_request' ) ) {
				litespeed_finish_request();
			}
			td_send_enquiry_mail( $post_id );
		}
	);

	return true;
}

/**
 * Email an enquiry to the site admin.
 *
 * @param int $post_id Enquiry ID.
 */
function td_send_enquiry_mail( $post_id ) {
	$get = static function ( $key ) use ( $post_id ) {
		return (string) get_post_meta( $post_id, '_td_' . $key, true );
	};
	$body = sprintf(
		"%s <%s>\nPhone: %s\nWork: %s\nArea: %s\nMix: %s\n\n%s",
		$get( 'name' ),
		$get( 'email' ),
		$get( 'phone' ),
		$get( 'kind' ),
		$get( 'area' ),
		$get( 'mix' ),
		$get( 'message' )
	);
	wp_mail(
		get_option( 'admin_email' ),
		sprintf( 'Floor enquiry from %s', $get( 'name' ) ),
		$body,
		array( 'Reply-To: ' . $get( 'email' ) )
	);
}

add_action(
	'rest_api_init',
	static function () {
		register_rest_route(
			'tessaro/v1',
			'/enquiry',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => static function ( WP_REST_Request $request ) {
					$result = td_save_enquiry( $request->get_params() );
					return is_wp_error( $result ) ? $result : array( 'ok' => true );
				},
			)
		);
	}
);

$td_enquiry_fallback = static function () {
	$valid  = isset( $_POST['td_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['td_nonce'] ), 'td_enquiry' );
	$result = $valid ? td_save_enquiry( wp_unslash( $_POST ) ) : new WP_Error( 'td_nonce', 'Expired' );
	wp_safe_redirect( add_query_arg( 'enquiry', is_wp_error( $result ) ? 'error' : 'sent', home_url( '/' ) ) . '#yard' );
	exit;
};
add_action( 'admin_post_td_enquiry', $td_enquiry_fallback );
add_action( 'admin_post_nopriv_td_enquiry', $td_enquiry_fallback );

add_action(
	'add_meta_boxes_td_enquiry',
	static function () {
		add_meta_box(
			'td_enquiry_box',
			__( 'What they told us', 'tessaro' ),
			static function ( WP_Post $post ) {
				echo '<table class="widefat striped"><tbody>';
				foreach ( array( 'name', 'email', 'phone', 'kind', 'area', 'mix', 'message' ) as $key ) {
					printf(
						'<tr><th style="width:120px;text-transform:capitalize">%s</th><td>%s</td></tr>',
						esc_html( $key ),
						nl2br( esc_html( (string) get_post_meta( $post->ID, '_td_' . $key, true ) ) )
					);
				}
				echo '</tbody></table>';
			},
			'td_enquiry',
			'normal',
			'high'
		);
	}
);
