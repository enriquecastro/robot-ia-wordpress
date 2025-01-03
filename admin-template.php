<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1>Configuración del Chatbot IA</h1>

    <form method="post" action="">
        <?php wp_nonce_field('wpai_chatbot_settings'); ?>

        <nav class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active">General</a>
            <a href="#ia" class="nav-tab">Configuración IA</a>
            <a href="#diseno" class="nav-tab">Diseño</a>
        </nav>

        <div class="tab-content">
            <!-- Pestaña General -->
            <div id="general" class="tab-pane active">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="api_key">API Key de OpenAI</label>
                        </th>
                        <td>
                            <input type="password" 
                                   name="api_key" 
                                   id="api_key" 
                                   value="<?php echo esc_attr($settings['api_key']); ?>" 
                                   class="regular-text">
                            <p class="description">Tu API Key de OpenAI</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="shortcode">Shortcode</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="shortcode" 
                                   value="[wpai_chatbot]" 
                                   readonly 
                                   class="regular-text">
                            <button type="button" class="button" onclick="copyShortcode()">Copiar</button>
                            <p class="description">Usa este shortcode para mostrar el chatbot en cualquier página</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Pestaña Configuración IA -->
            <div id="ia" class="tab-pane" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="company_name">Nombre de la Empresa</label>
                        </th>
                        <td>
                            <input type="text" 
                                   name="company_name" 
                                   id="company_name" 
                                   value="<?php echo esc_attr($settings['ai_settings']['company_name']); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="company_location">Ubicación</label>
                        </th>
                        <td>
                            <input type="text" 
                                   name="company_location" 
                                   id="company_location" 
                                   value="<?php echo esc_attr($settings['ai_settings']['company_location']); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="company_services">Servicios</label>
                        </th>
                        <td>
                            <textarea name="company_services" 
                                      id="company_services" 
                                      rows="4" 
                                      class="large-text"><?php echo esc_textarea($settings['ai_settings']['company_services']); ?></textarea>
                            <p class="description">Describe los servicios de tu empresa</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="system_prompt">Prompt del Sistema</label>
                        </th>
                        <td>
                            <textarea name="system_prompt" 
                                      id="system_prompt" 
                                      rows="6" 
                                      class="large-text"><?php echo esc_textarea($settings['ai_settings']['system_prompt']); ?></textarea>
                            <p class="description">Usa las variables: [nombre_empresa], [ubicacion], [servicios]</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="welcome_message">Mensaje de Bienvenida</label>
                        </th>
                        <td>
                            <input type="text" 
                                   name="welcome_message" 
                                   id="welcome_message" 
                                   value="<?php echo esc_attr($settings['ai_settings']['welcome_message']); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Pestaña Diseño -->
            <div id="diseno" class="tab-pane" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="background_color">Color de Fondo</label>
                        </th>
                        <td>
                            <input type="color" 
                                   name="background_color" 
                                   id="background_color" 
                                   value="<?php echo esc_attr($settings['design_settings']['background_color']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="text_color">Color del Texto</label>
                        </th>
                        <td>
                            <input type="color" 
                                   name="text_color" 
                                   id="text_color" 
                                   value="<?php echo esc_attr($settings['design_settings']['text_color']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="button_color">Color del Botón</label>
                        </th>
                        <td>
                            <input type="color" 
                                   name="button_color" 
                                   id="button_color" 
                                   value="<?php echo esc_attr($settings['design_settings']['button_color']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="chat_width">Ancho del Chat (px)</label>
                        </th>
                        <td>
                            <input type="number" 
                                   name="chat_width" 
                                   id="chat_width" 
                                   value="<?php echo esc_attr($settings['design_settings']['chat_width']); ?>" 
                                   min="300" 
                                   max="800">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="chat_height">Alto del Chat (px)</label>
                        </th>
                        <td>
                            <input type="number" 
                                   name="chat_height" 
                                   id="chat_height" 
                                   value="<?php echo esc_attr($settings['design_settings']['chat_height']); ?>" 
                                   min="300" 
                                   max="800">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="header_text">Texto del Encabezado</label>
                        </th>
                        <td>
                            <input type="text" 
                                   name="header_text" 
                                   id="header_text" 
                                   value="<?php echo esc_attr($settings['design_settings']['header_text']); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="footer_text">Texto del Pie</label>
                        </th>
                        <td>
                            <input type="text" 
                                   name="footer_text" 
                                   id="footer_text" 
                                   value="<?php echo esc_attr($settings['design_settings']['footer_text']); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <p class="submit">
            <input type="submit" 
                   name="wpai_chatbot_save" 
                   class="button-primary" 
                   value="Guardar Cambios">
        </p>
    </form>
</div>

<script>
// JavaScript para manejar las pestañas
jQuery(document).ready(function($) {
    // Manejar clics en las pestañas
    $('.nav-tab').click(function(e) {
        e.preventDefault();
        
        // Activar pestaña
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Mostrar contenido
        $('.tab-pane').hide();
        $($(this).attr('href')).show();
    });

    // Función para copiar shortcode
    window.copyShortcode = function() {
        var shortcode = document.getElementById('shortcode');
        shortcode.select();
        document.execCommand('copy');
        alert('Shortcode copiado al portapapeles');
    }
});
</script>
