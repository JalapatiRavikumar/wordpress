<?php
/**
 * Plugin Name: Feedback Web App
 * Description: A simple web app for collecting user feedback.
 * Version: 1.0
 * Author: Your Name
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register a custom post type for Feedback
function register_feedback_post_type() {
    $args = array(
        'labels' => array(
            'name' => __('Feedbacks'),
            'singular_name' => __('Feedback'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-feedback',
    );
    register_post_type('feedback', $args);
}
add_action('init', 'register_feedback_post_type');

// Create a shortcode for the feedback form
function feedback_form_shortcode() {
    ob_start();
    ?>
    <form method="post" id="feedbackForm">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        
        <label for="message">Feedback:</label>
        <textarea name="message" required></textarea>
        
        <input type="submit" name="submit_feedback" value="Submit Feedback">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('feedback_form', 'feedback_form_shortcode');

// Handle form submission
function handle_feedback_submission() {
    if (isset($_POST['submit_feedback'])) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);
        
        // Create a new feedback post
        $feedback_data = array(
            'post_title' => $name,
            'post_content' => $message,
            'post_type' => 'feedback',
            'post_status' => 'publish',
        );
        $feedback_id = wp_insert_post($feedback_data);
        
        // Add the email as post meta
        if ($feedback_id) {
            add_post_meta($feedback_id, 'feedback_email', $email);
            // Optionally, you can redirect or display a success message
            echo '<div>Thank you for your feedback!</div>';
        }
    }
}
add_action('wp', 'handle_feedback_submission');
