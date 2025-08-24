<?php
/**
 * Shortcode for WP Reading Time Estimator
 */

// [reading_time id="123"]
add_shortcode( 'reading_time', function( $atts ) {
    $atts = shortcode_atts( array(
        'id' => get_the_ID(),
    ), $atts, 'reading_time' );

    return wp_reading_time( $atts['id'] );
});
