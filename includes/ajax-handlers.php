<?php
if (!defined('ABSPATH')) exit;

// AJAX handler for Ask AI
function wpsi_handle_ask_ai() {
    header('Content-Type: application/json');

    if (empty($_POST['message'])) {
        wp_send_json_error(['error' => 'Missing message parameter.']);
    }

    $message = sanitize_text_field($_POST['message']);

    // Get API key, model, and model provider from options
    $api_key = get_option('wpsi_api_key', '');
    $selected_model = get_option('wpsi_ai_model', 'deepseek/deepseek-chat-v3-0324:free');
    $selected_model_provider = get_option('wpsi_ai_model_provider', 'OpenRouter');

    // Validate API key
    if (empty($api_key)) {
        wp_send_json_error([
            'error' => 'API key is not configured. Please go to Site Inspector → Settings and add your API key.'
        ]);
    }

    // Default to OpenRouter
    $api_url = 'https://openrouter.ai/api/v1/chat/completions';

    // Support for future expansion to other providers
    switch (strtolower($selected_model_provider)) {
        case 'openrouter':
        default:
            $api_url = 'https://openrouter.ai/api/v1/chat/completions';
            break;
        // Add more providers here as needed
    }

    $response = wp_remote_post($api_url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
            'HTTP-Referer'  => get_site_url(),
            'X-Title'       => get_bloginfo('name'),
        ],
        'body' => json_encode([
            'model' => $selected_model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant skilled in analyzing and fixing WordPress and PHP errors.',
                ],
                [
                    'role' => 'user',
                    'content' => $message,
                ],
            ]
        ]),
        'timeout' => 60,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['error' => $response->get_error_message()]);
    }

    $body = wp_remote_retrieve_body($response);
    $decoded = json_decode($body, true);

    // Log raw response for debugging
    error_log("OpenRouter raw response: " . print_r($body, true));

    if (!empty($decoded['choices'][0]['message']['content'])) {
        wp_send_json_success(['response' => $decoded['choices'][0]['message']['content']]);
    } elseif (!empty($decoded['error']['message'])) {
        wp_send_json_error(['error' => $decoded['error']['message']]);
    } else {
        wp_send_json_error(['error' => 'No valid AI response or error message received.', 'raw' => $body]);
    }
}

// Register AJAX actions
add_action('wp_ajax_wpsi_ask_ai', 'wpsi_handle_ask_ai');
add_action('wp_ajax_nopriv_wpsi_ask_ai', 'wpsi_handle_ask_ai');
