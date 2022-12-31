<?php
/**
 * Plugin Name: Is Author Online
 * Plugin URI: https://example.com/is-author-online
 * Description: A plugin that displays the online status of an author
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL2
 */

class Is_Author_Online {
    public function __construct() {
        add_shortcode('online_status', array($this, 'display_online_status'));
    }

    public function display_online_status($atts) {
        $atts = shortcode_atts(array(
            'online' => '',
            'offline' => '',
        ), $atts);

        if (is_user_logged_in()) {
            return '<img src="' . esc_url($atts['online']) . '" alt="Online">';
        } else {
            return '<img src="' . esc_url($atts['offline']) . '" alt="Offline">';
        }
    }
}

$is_author_online = new Is_Author_Online();
