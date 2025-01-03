<?php
/**
 * Plugin Name: WebProductiva AI Chatbot
 * Description: Un chatbot de IA personalizable para cualquier tipo de web
 * Version: 2.0
 * Author: WebProductiva
 * Author URI: https://webproductiva.es
 * Network: true
 */

if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('WPAI_CHATBOT_VERSION', '2.0');
define('WPAI_CHATBOT_PATH', plugin_dir_path(__FILE__));
define('WPAI_CHATBOT_URL', plugin_dir_url(__FILE__));

// Activación del plugin - Configuración inicial
function wpai_chatbot_activate() {
    $default_settings = array(
        'api_key' => '',
        'ai_settings' => array(
            'system_prompt' => 'Eres un asistente experto que representa a [nombre_empresa]. Datos de la empresa: - Ubicación: [ubicacion] - Servicios: [servicios] - NO proporciones información que no esté en esta lista.',
            'welcome_message' => '¡Hola! ¿En qué puedo ayudarte?',
            'input_placeholder' => 'Escribe tu pregunta aquí...',
            'company_name' => '',
            'company_location' => '',
            'company_services' => '',
        ),
        'design_settings' => array(
            'background_color' => '#000000',
            'text_color' => '#00ff00',
            'button_color' => '#008000',
            'chat_width' => '400',
            'chat_height' => '500',
            'font_family' => 'Courier New',
            'header_text' => 'AI Assistant',
            'footer_text' => 'Powered by AI'
        )
    );
    add_option('wpai_chatbot_settings', $default_settings);
}
register_activation_hook(__FILE__, 'wpai_chatbot_activate');

// Añadir menú de administración
function wpai_chatbot_admin_menu() {
    add_menu_page(
        'Configuración del Chatbot',
        'AI Chatbot',
        'manage_options',
        'wpai-chatbot-settings',
        'wpai_chatbot_admin_page',
        'dashicons-format-chat',
        30
    );
}
add_action('admin_menu', 'wpai_chatbot_admin_menu');

// Página de administración
function wpai_chatbot_admin_page() {
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        return;
    }

    $settings = get_option('wpai_chatbot_settings');

    // Guardar cambios
    if (isset($_POST['wpai_chatbot_save'])) {
        check_admin_referer('wpai_chatbot_settings');
        
        $settings['api_key'] = sanitize_text_field($_POST['api_key']);
        $settings['ai_settings']['system_prompt'] = sanitize_textarea_field($_POST['system_prompt']);
        $settings['ai_settings']['welcome_message'] = sanitize_text_field($_POST['welcome_message']);
        $settings['ai_settings']['company_name'] = sanitize_text_field($_POST['company_name']);
        $settings['ai_settings']['company_location'] = sanitize_text_field($_POST['company_location']);
        $settings['ai_settings']['company_services'] = sanitize_textarea_field($_POST['company_services']);
        
        $settings['design_settings']['background_color'] = sanitize_hex_color($_POST['background_color']);
        $settings['design_settings']['text_color'] = sanitize_hex_color($_POST['text_color']);
        $settings['design_settings']['button_color'] = sanitize_hex_color($_POST['button_color']);
        $settings['design_settings']['chat_width'] = absint($_POST['chat_width']);
        $settings['design_settings']['chat_height'] = absint($_POST['chat_height']);
        $settings['design_settings']['header_text'] = sanitize_text_field($_POST['header_text']);
        $settings['design_settings']['footer_text'] = sanitize_text_field($_POST['footer_text']);
        
        update_option('wpai_chatbot_settings', $settings);
        echo '<div class="updated"><p>Configuración guardada.</p></div>';
    }

    // Mostrar formulario de configuración
    include(WPAI_CHATBOT_PATH . 'admin-template.php');
}

// Shortcode del chatbot
function wpai_chatbot_shortcode() {
    $settings = get_option('wpai_chatbot_settings');
    
    wp_enqueue_script('jquery');
    wp_enqueue_script('wpai-chatbot-script', WPAI_CHATBOT_URL . 'script.js', array('jquery'), WPAI_CHATBOT_VERSION, true);
    wp_enqueue_style('wpai-chatbot-style', WPAI_CHATBOT_URL . 'style.css', array(), WPAI_CHATBOT_VERSION);
    
    wp_localize_script('wpai-chatbot-script', 'wpai_chatbot_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'settings' => $settings
    ));

    ob_start();
    ?>
    <div id="wpai-chatbot" 
         style="--chat-bg: <?php echo esc_attr($settings['design_settings']['background_color']); ?>;
                --chat-text: <?php echo esc_attr($settings['design_settings']['text_color']); ?>;
                --chat-button: <?php echo esc_attr($settings['design_settings']['button_color']); ?>;
                --chat-width: <?php echo esc_attr($settings['design_settings']['chat_width']); ?>px;
                --chat-height: <?php echo esc_attr($settings['design_settings']['chat_height']); ?>px;
                font-family: <?php echo esc_attr($settings['design_settings']['font_family']); ?>;">
        <div class="chat-header"><?php echo esc_html($settings['design_settings']['header_text']); ?></div>
        <div id="chat-messages"></div>
        <div class="chat-input-container">
            <input type="text" id="user-input" placeholder="<?php echo esc_attr($settings['ai_settings']['input_placeholder']); ?>">
            <button type="button" class="send-button">></button>
        </div>
        <div class="chat-footer"><?php echo esc_html($settings['design_settings']['footer_text']); ?></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('wpai_chatbot', 'wpai_chatbot_shortcode');

// Manejador AJAX
function wpai_chatbot_ajax_handler() {
    $settings = get_option('wpai_chatbot_settings');
    $message = sanitize_text_field($_POST['message']);

    if (empty($settings['api_key'])) {
        wp_send_json_error('API Key no configurada');
        wp_die();
    }

    // Preparar el prompt del sistema
    $system_prompt = str_replace(
        array('[nombre_empresa]', '[ubicacion]', '[servicios]'),
        array(
            $settings['ai_settings']['company_name'],
            $settings['ai_settings']['company_location'],
            $settings['ai_settings']['company_services']
        ),
        $settings['ai_settings']['system_prompt']
    );

    // Llamada a la API de OpenAI
    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $settings['api_key'],
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode(array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array('role' => 'system', 'content' => $system_prompt),
                array('role' => 'user', 'content' => $message)
            )
        ))
    ));

    if (is_wp_error($response)) {
        wp_send_json_error($response->get_error_message());
    } else {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($body['choices'][0]['message']['content'])) {
            wp_send_json_success(array('response' => $body['choices'][0]['message']['content']));
        } else {
            wp_send_json_error('Respuesta inesperada de la IA');
        }
    }
}
add_action('wp_ajax_wpai_chatbot', 'wpai_chatbot_ajax_handler');
add_action('wp_ajax_nopriv_wpai_chatbot', 'wpai_chatbot_ajax_handler');
