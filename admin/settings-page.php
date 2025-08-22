<?php
// === admin/settings-page.php ===
// Implements the Settings Page using WordPress Settings API

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Settings Page Output
function wprte_render_settings_page_content() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__( 'Reading Time Settings', 'wp-reading-time-estimator' ); ?></h1>

        <form method="post" action="options.php">
          <?php
            settings_fields( 'wprte_settings_group' ); // group
            do_settings_sections( 'wprte-settings' );  // page slug
            submit_button();
          ?>
        </form>

    </div>
    <?php
}

// Register settings, sections, and fields
add_action( 'admin_init', 'wprte_register_settings' );
function wprte_register_settings() {
    
    // Register option with the correct page slug
    register_setting(
        'wprte_settings_group',   // Group name
        'wprte_settings',         // Option name
        array(
            'sanitize_callback' => 'wprte_sanitize_settings',
        )
    );
    add_settings_section(
        'wprte_general_section',
        __( 'General Settings', 'wp-reading-time-estimator' ),
        '__return_false',
        'wprte-settings'
    );

    // Field: Post types
    add_settings_field(
        'wprte_post_types',
        __( 'Post Types', 'wp-reading-time-estimator' ),
        'wprte_field_post_types',
        'wprte-settings',
        'wprte_general_section'
    );

    // Field: Reading speed
    add_settings_field(
        'wprte_reading_speed',
        __( 'Reading Speed (words per minute)', 'wp-reading-time-estimator' ),
        'wprte_field_reading_speed',
        'wprte-settings',
        'wprte_general_section'
    );

    // Field: Extra seconds per image
    add_settings_field(
        'wprte_extra_seconds_image',
        __( 'Extra Seconds per Image', 'wp-reading-time-estimator' ),
        'wprte_field_extra_seconds_image',
        'wprte-settings',
        'wprte_general_section'
    );

    // Field: Auto inject location
    add_settings_field(
        'wprte_auto_inject',
        __( 'Auto-inject Location', 'wp-reading-time-estimator' ),
        'wprte_field_auto_inject',
        'wprte-settings',
        'wprte_general_section'
    );

    // Field: Output format
    add_settings_field(
        'wprte_output_format',
        __( 'Output Format', 'wp-reading-time-estimator' ),
        'wprte_field_output_format',
        'wprte-settings',
        'wprte_general_section'
    );

    // Field: Schema toggle
    add_settings_field(
        'wprte_enable_schema',
        __( 'Enable Schema Markup', 'wp-reading-time-estimator' ),
        'wprte_field_enable_schema',
        'wprte-settings',
        'wprte_general_section'
    );
}

// Sanitize callback
function wprte_sanitize_settings( $input ) {
    $output = array();
    $output['post_types']          = array_map( 'sanitize_text_field', (array) ( $input['post_types'] ?? array() ) );
    $output['reading_speed']       = absint( $input['reading_speed'] ?? 200 );
    $output['extra_seconds_image'] = absint( $input['extra_seconds_image'] ?? 10 );
    $output['auto_inject']         = in_array( $input['auto_inject'], array( 'before', 'after', 'none' ), true ) ? $input['auto_inject'] : 'none';
    $output['output_format']       = sanitize_text_field( $input['output_format'] ?? '%s min read' );
    $output['enable_schema']       = ! empty( $input['enable_schema'] ) ? 1 : 0;
    return $output;
}

// Field render callbacks
function wprte_field_post_types() {
    $options = get_option( 'wprte_settings' );
    $selected = $options['post_types'] ?? array( 'post' );
    $post_types = get_post_types( array( 'public' => true ), 'objects' );
    foreach ( $post_types as $pt ) {
        ?>
        <label><input type="checkbox" name="wprte_settings[post_types][]" value="<?php echo esc_attr( $pt->name ); ?>" <?php checked( in_array( $pt->name, $selected, true ) ); ?>> <?php echo esc_html( $pt->labels->singular_name ); ?></label><br>
        <?php
    }
}

function wprte_field_reading_speed() {
    $options = get_option( 'wprte_settings' );
    $value = $options['reading_speed'] ?? 200;
    ?>
    <input type="number" min="50" step="10" name="wprte_settings[reading_speed]" value="<?php echo esc_attr( $value ); ?>">
    <?php
}

function wprte_field_extra_seconds_image() {
    $options = get_option( 'wprte_settings' );
    $value = $options['extra_seconds_image'] ?? 10;
    ?>
    <input type="number" min="0" step="1" name="wprte_settings[extra_seconds_image]" value="<?php echo esc_attr( $value ); ?>">
    <?php
}

function wprte_field_auto_inject() {
    $options = get_option( 'wprte_settings' );
    $value = $options['auto_inject'] ?? 'none';
    ?>
    <select name="wprte_settings[auto_inject]">
        <option value="none" <?php selected( $value, 'none' ); ?>><?php _e( 'None', 'wp-reading-time-estimator' ); ?></option>
        <option value="before" <?php selected( $value, 'before' ); ?>><?php _e( 'Before Content', 'wp-reading-time-estimator' ); ?></option>
        <option value="after" <?php selected( $value, 'after' ); ?>><?php _e( 'After Content', 'wp-reading-time-estimator' ); ?></option>
    </select>
    <?php
}

function wprte_field_output_format() {
    $options = get_option( 'wprte_settings' );
    $value = $options['output_format'] ?? '%s min read';
    ?>
    <input type="text" name="wprte_settings[output_format]" value="<?php echo esc_attr( $value ); ?>" size="40">
    <p class="description"><?php _e( 'Use %s as placeholder for minutes.', 'wp-reading-time-estimator' ); ?></p>
    <?php
}

function wprte_field_enable_schema() {
    $options = get_option( 'wprte_settings' );
    $value = ! empty( $options['enable_schema'] );
    ?>
    <label><input type="checkbox" name="wprte_settings[enable_schema]" value="1" <?php checked( $value ); ?>> <?php _e( 'Add schema.org timeRequired markup', 'wp-reading-time-estimator' ); ?></label>
    <?php
}

// Render callback from admin class
if ( ! function_exists( 'wprte_render_settings_page' ) ) {
    function wprte_render_settings_page() {
        wprte_render_settings_page_content();
    }
}
