document.addEventListener('DOMContentLoaded', function() {
    console.log('WebProductiva AI Chatbot iniciado');
    initializeChatbot();
    showWelcomeMessage();
});

function initializeChatbot() {
    // Configurar eventos del input
    const userInput = document.getElementById('user-input');
    if (userInput) {
        userInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleUserMessage();
            }
        });
    }

    // Configurar botón de envío
    const sendButton = document.querySelector('.send-button');
    if (sendButton) {
        sendButton.addEventListener('click', function(e) {
            e.preventDefault();
            handleUserMessage();
        });
    }

    // Aplicar configuraciones personalizadas
    if (typeof wpai_chatbot_vars !== 'undefined' && wpai_chatbot_vars.settings) {
        applyCustomSettings(wpai_chatbot_vars.settings);
    }
}

function showWelcomeMessage() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages && wpai_chatbot_vars.settings.ai_settings.welcome_message) {
        chatMessages.innerHTML = `<p><strong>AI:</strong> ${wpai_chatbot_vars.settings.ai_settings.welcome_message}</p>`;
    }
}

function handleUserMessage() {
    const userInput = document.getElementById('user-input');
    const chatMessages = document.getElementById('chat-messages');
    const sendButton = document.querySelector('.send-button');
    
    if (!userInput || !chatMessages) return;

    const message = userInput.value.trim();
    if (message === '') return;

    // Deshabilitar entrada mientras se procesa
    userInput.disabled = true;
    sendButton.disabled = true;

    // Mostrar mensaje del usuario
    chatMessages.innerHTML += `<p><strong>Tú:</strong> ${escapeHtml(message)}</p>`;
    userInput.value = '';
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Mostrar indicador de escritura
    const typingIndicator = document.createElement('p');
    typingIndicator.className = 'typing-indicator';
    typingIndicator.innerHTML = '<strong>AI:</strong> escribiendo...';
    chatMessages.appendChild(typingIndicator);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Enviar mensaje al servidor
    jQuery.ajax({
        url: wpai_chatbot_vars.ajax_url,
        type: 'POST',
        data: {
            action: 'wpai_chatbot',
            message: message
        },
        success: function(response) {
            // Eliminar indicador de escritura
            typingIndicator.remove();

            if (response.success) {
                chatMessages.innerHTML += `<p><strong>AI:</strong> ${response.data.response}</p>`;
            } else {
                chatMessages.innerHTML += `<p class="wpai-chatbot-error"><strong>Error:</strong> ${response.data}</p>`;
            }
            chatMessages.scrollTop = chatMessages.scrollHeight;
        },
        error: function(xhr, status, error) {
            // Eliminar indicador de escritura
            typingIndicator.remove();

            console.error('Error en la petición:', error);
            chatMessages.innerHTML += `<p class="wpai-chatbot-error"><strong>Error:</strong> No se pudo conectar con el servidor.</p>`;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        },
        complete: function() {
            // Reactivar entrada
            userInput.disabled = false;
            sendButton.disabled = false;
            userInput.focus();
        }
    });
}

function applyCustomSettings(settings) {
    const chatbot = document.getElementById('wpai-chatbot');
    if (!chatbot) return;

    // Aplicar variables CSS personalizadas
    chatbot.style.setProperty('--chat-bg', settings.design_settings.background_color);
    chatbot.style.setProperty('--chat-text', settings.design_settings.text_color);
    chatbot.style.setProperty('--chat-button', settings.design_settings.button_color);
    chatbot.style.setProperty('--chat-width', `${settings.design_settings.chat_width}px`);
    chatbot.style.setProperty('--chat-height', `${settings.design_settings.chat_height}px`);
    
    // Actualizar placeholder
    const userInput = document.getElementById('user-input');
    if (userInput && settings.ai_settings.input_placeholder) {
        userInput.placeholder = settings.ai_settings.input_placeholder;
    }
}

// Función de utilidad para escapar HTML y prevenir XSS
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Función para limpiar el historial
function clearChatHistory() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.innerHTML = '';
        showWelcomeMessage();
    }
}

// Función para manejar errores de red
window.addEventListener('online', function() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.innerHTML += `<p class="wpai-chatbot-success"><strong>Sistema:</strong> Conexión restaurada.</p>`;
    }
});

window.addEventListener('offline', function() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.innerHTML += `<p class="wpai-chatbot-error"><strong>Sistema:</strong> Sin conexión a internet.</p>`;
    }
});

// Manejar visibilidad para pausar/reanudar
document.addEventListener('visibilitychange', function() {
    const userInput = document.getElementById('user-input');
    if (userInput) {
        userInput.disabled = document.hidden;
    }
});
