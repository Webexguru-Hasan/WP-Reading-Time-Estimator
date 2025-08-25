<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * -------------------------------------------------------
 *  Check if Progress Bar should load
 * -------------------------------------------------------
 */
function wprte_progress_should_load() {
    if ( is_admin() ) {
        return false;
    }

    $options = get_option( 'wprte_settings', array() );

    // Disabled in settings
    if ( empty( $options['progress_enable'] ) ) {
        return false;
    }

    // Only for single posts/pages
    if ( ! is_singular() ) {
        return false;
    }

    // Respect enabled post types
    $enabled = ! empty( $options['post_types'] ) ? (array) $options['post_types'] : array( 'post' );
    $pt = get_post_type();

    return in_array( $pt, $enabled, true );
}

/**
 * -------------------------------------------------------
 *  Enqueue Progress Bar Assets (CSS + JS)
 * -------------------------------------------------------
 */
function wprte_progress_enqueue_assets() {
    if ( ! wprte_progress_should_load() ) {
        return;
    }

    $options = get_option( 'wprte_settings', array() );

    // CSS
    wp_enqueue_style(
        'wprte-progress',
        WPRTE_URL . '/public/css/wprte-progress.css',
        array(),
        WPRTE_VERSION
    );

    // JS
    wp_enqueue_script(
        'wprte-progress',
        WPRTE_URL . '/public/js/wprte-progress.js',
        array(),
        WPRTE_VERSION,
        true
    );

    // Pass settings to JS
    wp_localize_script( 'wprte-progress', 'WPRTE_PROGRESS', array(
        'position' => ! empty( $options['progress_position'] ) ? $options['progress_position'] : 'top',
        'height'   => ! empty( $options['progress_height'] ) ? absint( $options['progress_height'] ) : 3,
        'color'    => ! empty( $options['progress_color'] ) ? $options['progress_color'] : '#3b82f6',
    ) );
}
add_action( 'wp_enqueue_scripts', 'wprte_progress_enqueue_assets' );

/**
 * -------------------------------------------------------
 *  Render Progress Bar in Footer (Auto Inject Support)
 * -------------------------------------------------------
 */
function wprte_render_progress_bar() {
    if ( ! wprte_progress_should_load() ) {
        return;
    }

    $options = get_option( 'wprte_settings', array() );
    $auto_inject = $options['auto_inject'] ?? 'none';

    // Only auto-inject if "before" or "after" is chosen
    if ( in_array( $auto_inject, array( 'before', 'after' ), true ) ) {
        echo '<div id="wprte-progress"><span class="wprte-progress-bar" aria-hidden="true"></span></div>';
    }
}
add_action( 'wp_footer', 'wprte_render_progress_bar', 20 );

/**
 * -------------------------------------------------------
 *  Auto Inject Progress Bar Before/After Content
 * -------------------------------------------------------
 */
function wprte_progress_auto_inject( $content ) {
    if ( ! wprte_progress_should_load() ) {
        return $content;
    }

    $options = get_option( 'wprte_settings', array() );
    $auto_inject = $options['auto_inject'] ?? 'none';

    $bar_html = '<div id="wprte-progress"><span class="wprte-progress-bar" aria-hidden="true"></span></div>';

    if ( $auto_inject === 'before' ) {
        return $bar_html . $content;
    }

    if ( $auto_inject === 'after' ) {
        return $content . $bar_html;
    }

    return $content;
}
add_filter( 'the_content', 'wprte_progress_auto_inject' );
