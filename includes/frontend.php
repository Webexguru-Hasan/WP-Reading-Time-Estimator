<?php
/**
 * Frontend Logic – Reading Time Calculation
 * Part of WP Reading Time Estimator
 */

// Calculate reading time for a post
function wprte_calculate_reading_time( $post_id ) {
    $options = get_option( 'wprte_settings', array() );

    $wpm       = ! empty( $options['reading_speed'] ) ? absint( $options['reading_speed'] ) : 200;
    $img_time  = ! empty( $options['extra_seconds_image'] ) ? absint( $options['extra_seconds_image'] ) : 0;
    $content   = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( wp_strip_all_tags( $content ) );

    // Count images
    $img_count = substr_count( strtolower( $content ), '<img ' );

    // Base minutes
    $minutes = ceil( $word_count / $wpm );

    // Add seconds from images
    $extra = ceil( ( $img_count * $img_time ) / 60 );

    $total_minutes = max( 1, $minutes + $extra );

    return $total_minutes;
}

// Get formatted reading time (with schema if enabled)
function wp_reading_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $options = get_option( 'wprte_settings', array() );
    $minutes = wprte_calculate_reading_time( $post_id );

    // Output format
    $format = ! empty( $options['output_format'] ) ? $options['output_format'] : '%s min read';
    $text   = sprintf( $format, $minutes );

    // Add schema markup if enabled
    if ( ! empty( $options['enable_schema'] ) ) {
        $text .= sprintf(
            '<meta itemprop="timeRequired" content="PT%dM" />',
            $minutes
        );
    }

    return $text;
}

// Echo function
function the_reading_time( $post_id = null ) {
    echo wp_reading_time( $post_id );
}

// Auto inject into content
// Auto inject into content
add_filter( 'the_content', function( $content ) {
    if ( is_admin() ) {
        return $content;
    }

    // শুধু singular পেজে চালাই; Elementor/Builder-এ main_query/loop নাও হতে পারে, তাই সেগুলো শর্ত থেকে বাদ দিলাম
    if ( is_singular() ) {
        $options = get_option( 'wprte_settings', array() );

         // Check post type
        $post_type     = get_post_type();
        $enabled_types = ! empty( $options['post_types'] ) ? (array) $options['post_types'] : array( 'post' );

        if ( in_array( $post_type, $enabled_types, true ) ) {
            
            $location = ! empty( $options['auto_inject'] ) ? $options['auto_inject'] : 'none';

            if ( strpos( $content, 'wprte-reading-time' ) !== false ) {
                return $content;
            }

            // error_log( sprintf( 'WPRTE inject: location=%s, post_type=%s', $location, $post_type ) );

            if ( $location === 'before' ) {
                return '<div class="wprte-reading-time">' . wp_reading_time() . '</div>' . $content;
            } elseif ( $location === 'after' ) {
                return $content . '<div class="wprte-reading-time">' . wp_reading_time() . '</div>';
            }
        }
    }

    return $content;
});


// Shortcode [reading_time]
// add_shortcode( 'reading_time', function( $atts ) {
//     $atts = shortcode_atts( array(
//         'id' => get_the_ID(),
//     ), $atts, 'reading_time' );

//     return wp_reading_time( $atts['id'] );
// });
