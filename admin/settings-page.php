<?php
// === admin/settings-page.php ===
// Implements the Settings Page using WordPress Settings API

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ================================
 * Settings Page Rendering
 * ================================
 */
function wprte_render_settings_page_content() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__( 'Reading Time Settings', 'wp-reading-time-estimator' ); ?></h1>

        <form method="post" action="options.php">
            <?php
                // Register + output security fields for the form
                settings_fields( 'wprte_settings' );
                do_settings_sections( 'wprte-settings' );
                submit_button();
            ?>
        </form>

        <hr />

        <!-- Shortcode Section for Reading time estimator-->
        <h2><?php esc_html_e( 'Shortcode for Reading Time', 'wp-reading-time-estimator' ); ?></h2>
        <p><?php esc_html_e( 'You can display the reading time anywhere using this shortcode. also before using shortcode please make "Auto-inject Location" None', 'wp-reading-time-estimator' ); ?></p>

        <div style="background:#f9f9f9; border:1px solid #ddd; padding:5px; border-radius:6px; display:inline-block;">
            <code>[reading_time]</code>
            <button type="button" class="button"
                onclick="navigator.clipboard.writeText('[reading_time]'); alert('Shortcode copied!');">
                <?php esc_html_e( 'Copy', 'wp-reading-time-estimator' ); ?>
            </button>
            
        </div>


    </div>
    <?php
}

/**
 * ================================
 * Register Settings + Fields
 * ================================
 */
add_action( 'admin_init', 'wprte_register_settings' );

function wprte_register_settings() {

    // Register settings
    register_setting(
        'wprte_settings',
        'wprte_settings',
        array(
            'sanitize_callback' => 'wprte_sanitize_settings',
        )
    );

    // Section: General
    add_settings_section(
        'wprte_general_section',
        __( 'General Settings', 'wp-reading-time-estimator' ),
        '__return_false',
        'wprte-settings'
    );

    // Fields
    add_settings_field(
        'wprte_post_types',
        __( 'Post Types', 'wp-reading-time-estimator' ),
        'wprte_field_post_types',
        'wprte-settings',
        'wprte_general_section'
    );

    add_settings_field(
        'wprte_reading_speed',
        __( 'Reading Speed (words per minute)', 'wp-reading-time-estimator' ),
        'wprte_field_reading_speed',
        'wprte-settings',
        'wprte_general_section'
    );

    add_settings_field(
        'wprte_extra_seconds_image',
        __( 'Extra Seconds per Image', 'wp-reading-time-estimator' ),
        'wprte_field_extra_seconds_image',
        'wprte-settings',
        'wprte_general_section'
    );

    add_settings_field(
        'wprte_auto_inject',
        __( 'Auto-inject Location', 'wp-reading-time-estimator' ),
        'wprte_field_auto_inject',
        'wprte-settings',
        'wprte_general_section'
    );

    add_settings_field(
        'wprte_output_format',
        __( 'Output Format', 'wp-reading-time-estimator' ),
        'wprte_field_output_format',
        'wprte-settings',
        'wprte_general_section'
    );

    add_settings_field(
        'wprte_enable_schema',
        __( 'Enable Schema Markup', 'wp-reading-time-estimator' ),
        'wprte_field_enable_schema',
        'wprte-settings',
        'wprte_general_section'
    );

//Prograss Bar---------
    // Section: Progress Bar
add_settings_section(
    'wprte_progress_section',
    __( 'Reading Progress Bar', 'wp-reading-time-estimator' ),
    '__return_false',
    'wprte-settings'
);

// Field: Enable
add_settings_field(
    'wprte_progress_enable',
    __( 'Enable Progress Bar', 'wp-reading-time-estimator' ),
    'wprte_field_progress_enable',
    'wprte-settings',
    'wprte_progress_section'
);

// Field: Position
add_settings_field(
    'wprte_progress_position',
    __( 'Position', 'wp-reading-time-estimator' ),
    'wprte_field_progress_position',
    'wprte-settings',
    'wprte_progress_section'
);

// Field: Height
add_settings_field(
    'wprte_progress_height',
    __( 'Height (px)', 'wp-reading-time-estimator' ),
    'wprte_field_progress_height',
    'wprte-settings',
    'wprte_progress_section'
);

// Field: Color
add_settings_field(
    'wprte_progress_color',
    __( 'Bar Color', 'wp-reading-time-estimator' ),
    'wprte_field_progress_color',
    'wprte-settings',
    'wprte_progress_section'
);

}

/**
 * ================================
 * Sanitize Settings
 * ================================
 */
function wprte_sanitize_settings( $input ) {
    $output = array();

    // General
    $output['post_types']          = array_map( 'sanitize_text_field', (array) ( $input['post_types'] ?? array() ) );
    $output['reading_speed']       = absint( $input['reading_speed'] ?? 200 );
    $output['extra_seconds_image'] = absint( $input['extra_seconds_image'] ?? 10 );
    $output['auto_inject']         = in_array( $input['auto_inject'] ?? 'none', array( 'before', 'after', 'none' ), true ) ? $input['auto_inject'] : 'none';
    $output['output_format']       = sanitize_text_field( $input['output_format'] ?? '%s min read' );
    $output['enable_schema']       = ! empty( $input['enable_schema'] ) ? 1 : 0;

    // Progress Bar (IMPORTANT: keep this BEFORE the return)
    $output['progress_enable']    = ! empty( $input['progress_enable'] ) ? 1 : 0;
    $pos                          = $input['progress_position'] ?? 'top';
    $output['progress_position']  = in_array( $pos, array( 'top', 'bottom' ), true ) ? $pos : 'top';
    $output['progress_height']    = max( 1, absint( $input['progress_height'] ?? 3 ) );
    $color                        = $input['progress_color'] ?? '#3b82f6';
    $output['progress_color']     = sanitize_hex_color( $color ) ?: '#3b82f6';

    return $output;
}



/**
 * ================================
 * Field Callbacks
 * ================================
 */
function wprte_field_post_types() {
    $options    = get_option( 'wprte_settings' );
    $selected   = $options['post_types'] ?? array( 'post' );
    $post_types = get_post_types( array( 'public' => true ), 'objects' );

    foreach ( $post_types as $pt ) {
        ?>
        <label>
            <input type="checkbox" name="wprte_settings[post_types][]"
                value="<?php echo esc_attr( $pt->name ); ?>"
                <?php checked( in_array( $pt->name, $selected, true ) ); ?>>
            <?php echo esc_html( $pt->labels->singular_name ); ?>
        </label><br>
        <?php
    }
}

function wprte_field_reading_speed() {
    $options = get_option( 'wprte_settings' );
    $value   = $options['reading_speed'] ?? 200;
    ?>
    <input type="number" min="50" step="10" name="wprte_settings[reading_speed]" value="<?php echo esc_attr( $value ); ?>">
    <?php
}

function wprte_field_extra_seconds_image() {
    $options = get_option( 'wprte_settings' );
    $value   = $options['extra_seconds_image'] ?? 10;
    ?>
    <input type="number" min="0" step="1" name="wprte_settings[extra_seconds_image]" value="<?php echo esc_attr( $value ); ?>">
    <?php
}

function wprte_field_auto_inject() {
    $options = get_option( 'wprte_settings' );
    $value   = $options['auto_inject'] ?? 'none';
    ?>
    <select name="wprte_settings[auto_inject]">
        <option value="none"   <?php selected( $value, 'none' ); ?>><?php _e( 'None', 'wp-reading-time-estimator' ); ?></option>
        <option value="before" <?php selected( $value, 'before' ); ?>><?php _e( 'Before Content', 'wp-reading-time-estimator' ); ?></option>
        <option value="after"  <?php selected( $value, 'after' ); ?>><?php _e( 'After Content', 'wp-reading-time-estimator' ); ?></option>
    </select>
    <?php
}

function wprte_field_output_format() {
    $options = get_option( 'wprte_settings' );
    $value   = $options['output_format'] ?? '%s min read';
    ?>
    <input type="text" name="wprte_settings[output_format]" value="<?php echo esc_attr( $value ); ?>" size="40">
    <p class="description"><?php _e( 'Use %s as placeholder for minutes.', 'wp-reading-time-estimator' ); ?></p>
    <?php
}

function wprte_field_enable_schema() {
    $options = get_option( 'wprte_settings' );
    $value   = ! empty( $options['enable_schema'] );
    ?>
    <label>
        <input type="checkbox" name="wprte_settings[enable_schema]" value="1" <?php checked( $value ); ?>>
        <?php _e( 'Add schema.org timeRequired markup', 'wp-reading-time-estimator' ); ?>
    </label>
    <?php
}


//Progress Bar
function wprte_field_progress_enable() {
    $o = get_option( 'wprte_settings' );
    $val = ! empty( $o['progress_enable'] );
    ?>
    <label>
        <input type="checkbox" name="wprte_settings[progress_enable]" value="1" <?php checked( $val ); ?>>
        <?php esc_html_e( 'Show a small reading progress bar on single posts/pages.', 'wp-reading-time-estimator' ); ?>
    </label>
    <?php
}

function wprte_field_progress_position() {
    $o = get_option( 'wprte_settings' );
    $val = $o['progress_position'] ?? 'top';
    ?>
    <select name="wprte_settings[progress_position]">
        <option value="top" <?php selected( $val, 'top' ); ?>><?php esc_html_e( 'Top', 'wp-reading-time-estimator' ); ?></option>
        <option value="bottom" <?php selected( $val, 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'wp-reading-time-estimator' ); ?></option>
    </select>
    <?php
}

function wprte_field_progress_height() {
    $o = get_option( 'wprte_settings' );
    $val = $o['progress_height'] ?? 3;
    ?>
    <input type="number" min="1" step="1" name="wprte_settings[progress_height]" value="<?php echo esc_attr( $val ); ?>" style="width:80px;">
    <?php
}

function wprte_field_progress_color() {
    $o = get_option( 'wprte_settings' );
    $val = $o['progress_color'] ?? '#3b82f6';
    ?>
    <input type="text" name="wprte_settings[progress_color]" value="<?php echo esc_attr( $val ); ?>" class="regular-text" placeholder="#3b82f6">
    <p class="description"><?php esc_html_e( 'Use a HEX color (e.g., #3b82f6).', 'wp-reading-time-estimator' ); ?></p>
    <?php
}

/**
 * ================================
 * Render Callback (used in Admin Class)
 * ================================
 */
if ( ! function_exists( 'wprte_render_settings_page' ) ) {
    function wprte_render_settings_page() {
        wprte_render_settings_page_content();
    }
}
