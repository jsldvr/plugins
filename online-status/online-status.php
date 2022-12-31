<?php
/**
 * Plugin Name: Online Status
 * Plugin URI: https://example.com/online-status
 * Description: A plugin that uses the Heartbeat API to check the online status of the author of a post or widget, and display it using a shortcode
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL2
 */

class Online_Status {
    public function __construct() {
        add_shortcode('online_status', array($this, 'display_online_status'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_check_online_status', array($this, 'check_online_status'));
        add_action('wp_ajax_nopriv_check_online_status', array($this, 'check_online_status'));
    }

    public function display_online_status($atts) {
        $atts = shortcode_atts(array(
            'author_id' => get_the_author_meta('ID'),
            'online' => '',
            'offline' => '',
        ), $atts);

        $output = '<div class="online-status" data-author-id="' . esc_attr($atts['author_id']) . '" data-online="' . esc_url($atts['online']) . '" data-offline="' . esc_url($atts['offline']) . '">';
        $output .= '<img src="' . esc_url($atts['offline']) . '" alt="Offline">';
        $output .= '</div>';

        return $output;
    }

    public function enqueue_scripts() {
        wp_enqueue_script('online-status', plugin_dir_url(__FILE__) . 'online-status.js', array('jquery'), '1.0.0', true);
        wp_localize_script('online-status', 'online_status', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'interval' => MINUTE_IN_SECONDS,
        ));
    }

    public function check_online_status() {
        $author_id = isset($_POST['author_id']) ? intval($_POST['author_id']) : 0;
    
        if (!$author_id) {
            wp_send_json_error();
        }
    
        $last_activity = get_user_meta($author_id, 'last_activity', true);
        $online_threshold = 5 * MINUTE_IN_SECONDS; // 5 minutes
        $now = time();
    
        if ($last_activity && ($now - $last_activity) < $online_threshold) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }
}    

$online_status = new Online_Status();