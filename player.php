<?php
/*
Plugin Name: Player SA
Description: Plugin para mostrar un reproductor de audio.
Version: 1.0
Author: SA Publicidad SpA
Author URI: https://www.sapublicidad.cl
*/

// Registro del menú
function custom_audio_player_menu() {
    add_menu_page(
        'Reproductor de Radio', // Título de la página
        'Reproductor de Radio', // Texto del menú
        'manage_options', // Capacidad requerida para ver la página
        'custom_audio_player_options', // Slug de la página
        'custom_audio_player_options_page_callback', // Función de renderizado de la página
        'dashicons-controls-play', // Ícono del menú
        20 // Posición en el menú
    );
}
add_action('admin_menu', 'custom_audio_player_menu');

// Registro de la página de opciones
function custom_audio_player_options_page() {
    add_options_page('Configuración del Reproductor de Radio', 'Reproductor de Radio', 'manage_options', 'custom_audio_player_options', 'custom_audio_player_options_page_callback');
}
add_action('admin_menu', 'custom_audio_player_options_page');

// Configuración de las opciones
function custom_audio_player_options_page_callback() {
    ?>
    <div class="wrap">
        <h1>Configuración del Reproductor de Radio</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_audio_player_options');
            do_settings_sections('custom_audio_player_options');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Registro de las opciones
function custom_audio_player_register_options() {
    register_setting('custom_audio_player_options', 'custom_audio_player_settings', 'custom_audio_player_sanitize_options');

    add_settings_section('custom_audio_player_section', 'Configuración del Reproductor de Radio', '', 'custom_audio_player_options');

    add_settings_field('stream_url', 'URL del Flujo de Audio', 'custom_audio_player_stream_url_callback', 'custom_audio_player_options', 'custom_audio_player_section');
    add_settings_field('on_air_text', 'Texto "Al Aire"', 'custom_audio_player_on_air_text_callback', 'custom_audio_player_options', 'custom_audio_player_section');
    add_settings_field('radio_name', 'Nombre de la Radio', 'custom_audio_player_radio_name_callback', 'custom_audio_player_options', 'custom_audio_player_section');
}
add_action('admin_init', 'custom_audio_player_register_options');

// Función de devolución de llamada para la URL del flujo de audio
function custom_audio_player_stream_url_callback() {
    $options = get_option('custom_audio_player_settings');
    $stream_url = isset($options['stream_url']) ? esc_url($options['stream_url']) : '';

    echo '<input type="text" id="stream_url" name="custom_audio_player_settings[stream_url]" value="' . $stream_url . '" class="regular-text">';
    echo '<p class="description">Ingrese la URL del flujo de audio para el reproductor.</p>';
}

// Función de devolución de llamada para el texto "Al Aire"
function custom_audio_player_on_air_text_callback() {
    $options = get_option('custom_audio_player_settings');
    $on_air_text = isset($options['on_air_text']) ? esc_html($options['on_air_text']) : 'Al Aire';

    echo '<input type="text" id="on_air_text" name="custom_audio_player_settings[on_air_text]" value="' . $on_air_text . '" class="regular-text">';
    echo '<p class="description">Ingrese el texto para "Al Aire" en el reproductor.</p>';
}

// Función de devolución de llamada para el nombre de la radio
function custom_audio_player_radio_name_callback() {
    $options = get_option('custom_audio_player_settings');
    $radio_name = isset($options['radio_name']) ? esc_html($options['radio_name']) : 'Radio Mystic';

    echo '<input type="text" id="radio_name" name="custom_audio_player_settings[radio_name]" value="' . $radio_name . '" class="regular-text">';
    echo '<p class="description">Ingrese el nombre de la radio para mostrar en el reproductor.</p>';
}

// Sanitización de las opciones
function custom_audio_player_sanitize_options($input) {
    $sanitized_input = array();

    if (isset($input['stream_url'])) {
        $sanitized_input['stream_url'] = esc_url_raw($input['stream_url']);
    }

    if (isset($input['on_air_text'])) {
        $sanitized_input['on_air_text'] = sanitize_text_field($input['on_air_text']);
    }

    if (isset($input['radio_name'])) {
        $sanitized_input['radio_name'] = sanitize_text_field($input['radio_name']);
    }

    return $sanitized_input;
}

// Enqueue de estilos y scripts
function custom_audio_player_scripts() {
    wp_enqueue_style('custom-audio-player-style', plugins_url('assets/styles.css?2&', __FILE__));
    wp_enqueue_script('custom-audio-player-script', plugins_url('assets/player.js?2&', __FILE__), array('jquery'), null, true);

    // Localiza las variables para el script
    $options = get_option('custom_audio_player_settings');
    wp_localize_script('custom-audio-player-script', 'audio_player_vars', array(
        'stream_url' => isset($options['stream_url']) ? $options['stream_url'] : '',
        'on_air_text' => isset($options['on_air_text']) ? $options['on_air_text'] : 'Al Aire',
        'radio_name' => isset($options['radio_name']) ? $options['radio_name'] : 'Radio Mystic',
    ));
}
add_action('wp_enqueue_scripts', 'custom_audio_player_scripts');

// Función para mostrar el reproductor
function custom_audio_player_function() {
    $options = get_option('custom_audio_player_settings');
    ?>
    <div id="player-container">
        <div id="radio-image">
            <?php
            $custom_logo_id = get_theme_mod('custom_logo');
            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
            if (has_custom_logo()) {
                echo '<img src="' . esc_url($logo[0]) . '">';
            } else {
                ?>
                <img src="<?php bloginfo('template_url') ?>/img/logo.jpg" alt="<?php wp_title(''); ?>" width="100%">
                <?php
            }
            ?>
        </div>
        <div class="player-info">
            <h3><b><?php echo esc_html($options['on_air_text']); ?> <span></span></b></h3>
            <h2><?php echo esc_html($options['radio_name']); ?></h2>
        </div>
        <div id="player-controls">
            <button id="play-pause-button"><i class="fas fa-play"></i></button>
            <input type="range" id="volume-slider" min="0" max="1" step="0.01" value="1">
        </div>
    </div>
    <?php
}
add_shortcode('custom_audio_player', 'custom_audio_player_function');
