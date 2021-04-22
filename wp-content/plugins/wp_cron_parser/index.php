<?php

/**
 * Plugin Name: My WP-Cron Test
 * Description: To execute cron job
 * Version: 1.0
 * Author: Author Name
 * Author URI: http://www.facebook.com/sahilgualti007
 **/

add_action( 'parse_news', 'parse_news' );

function myplugin_active() {
	if ( ! wp_next_scheduled( 'pt_my_task_hook' ) ) {

		wp_schedule_event( time(), 'hourly', 'parse_news' );
		wp_schedule_event( time(), 'every_three_minutes', 'parse_news' );
		wp_schedule_event( strtotime( '22:00:00' ), 'daily', 'pt_my_task_hook' );

	}
}

function parse_news() {
	$buisness = "https://annalshub.com/category/buisness/";
	$sports   = "https://annalshub.com/category/sports/";

	function get_html( $url ) {
		$data = file_get_contents( $url );

		return $data;
	}

	function parse( $p1, $p2, $p3 ) {
		$num1 = strpos( $p1, $p2 );
		if ( $num1 === false ) {
			return 0;
		}
		$num2 = substr( $p1, $num1 );

		return strip_tags( substr( $num2, 0, strpos( $num2, $p3 ) ) );
	}

	$business = get_html( $buisness );
	$sports   = get_html( $sports );

	$business_obj = (object) array(
		'title'       => parse( $buisness, "<div class='td-meta-info-container'>", "</div>" ),
		'date'        => parse( $buisness, "<div class='td-meta-info-container'>", "</div>" ),
		'description' => parse( $buisness, "<div class='td-meta-info-container'>", "</div>" ),
		'img-url'     => parse( $buisness, "<div class='td-meta-info-container'>", "</div>" ),
	);

	$sports_obj = new \stdClass;
	$sports_obj = (object) array(
		'title'       => parse( $sports, "<div class='td-meta-info-container'>", "</div>" ),
		'date'        => parse( $sports, "<div class='td-meta-info-container'>", "</div>" ),
		'description' => parse( $sports, "<div class='td-meta-info-container'>", "</div>" ),
		'img-url'     => parse( $sports, "<div class='td-meta-info-container'>", "</div>" ),
	);

	wp_cron( '' );

}

register_activation_hook(__FILE__,'myplugin_active');
