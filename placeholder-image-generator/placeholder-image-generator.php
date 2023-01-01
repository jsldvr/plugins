<?php

/*
Plugin Name: Placeholder Image Generator
Description: Generates placeholder images from the media library and saves them for later use.
*/

class Placeholder_Image_Generator {
    // plugin initialization function
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_generate_placeholder_image', array( $this, 'ajax_generate_placeholder_image' ) );
        add_action( 'wp_ajax_save_placeholder_image', array( $this, 'ajax_save_placeholder_image' ) );
        add_action( 'admin_footer', array( $this, 'admin_interface' ) );
    }

    // enqueue scripts and styles
    public function enqueue_scripts() {
        wp_enqueue_style( 'placeholder-image-generator', plugin_dir_url( __FILE__ ) . 'css/style.css' );
        wp_enqueue_script( 'placeholder-image-generator', plugin_dir_url( __FILE__ ) . 'js/script.js', array( 'jquery' ), '1.0', true );
    }

    // generate placeholder image via ajax
    public function ajax_generate_placeholder_image() {
        $width = intval( $_POST['width'] );
        $height = intval( $_POST['height'] );

        // create image
        $image = imagecreatetruecolor( $width, $height );
        imagealphablending( $image, false );
        imagesavealpha( $image, true );

        // set background color
        $bg_color = imagecolorallocatealpha( $image, 0x41, 0x41, 0x41, 0 );
        imagefill( $image, 0, 0, $bg_color );

        // set text color
        $text_color = imagecolorallocate( $image, 255, 255, 255 );

        // write text
        imagestring( $image, 5, $width / 2 - 20, $height / 2 - 10, 'Placeholder', $text_color );

        // make sure tmp directory exists
        if ( ! is_dir( plugin_dir_path( __FILE__ ) . 'tmp' ) ) {
            mkdir( plugin_dir_path( __FILE__ ) . 'tmp' );
        }

        // generate filename
        $filename = sprintf( 'placeholder-%dx%d.png', $width, $height );
        $filepath = plugin_dir_path( __FILE__ ) . 'tmp/' . $filename;

        // save image
        imagepng( $image, $filepath );
        imagedestroy( $image );

        // send response
        wp_send_json_success( array(
            'url' => plugin_dir_url( __FILE__ ) . 'tmp/' . $filename
        ) );
    }

    // save placeholder image to media library via ajax
    public function ajax_save_placeholder_image() {
        $url = esc_url_raw( $_POST['url'] );

        // download image
        $response = wp_remote_get( $url );
        $body = wp_remote_retrieve_body( $response );

        // generate filename
        $filename = basename( $url );
        $upload_dir = wp_upload_dir();
        $filepath = $upload_dir['path'] . '/' . $filename;

        // save image to file
        file_put_contents( $filepath, $body );

        // insert image into media library
        $attachment = array(
            'guid'           => $upload_dir['url'] . '/' . $filename,
            'post_mime_type' => 'image/png',
            'post_title'     => $filename,
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $filepath );
        wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $filepath ) );

        // send response
        wp_send_json_success( array(
            'id' => $attach_id
        ) );
    }
    // display admin interface
    public function admin_interface() {
        ?>
        <div id="placeholder-image-generator">
            <form>
                <label for="width">Width:</label>
                <input type="number" name="width" min="1" value="640">
                <label for="height">Height:</label>
                <input type="number" name="height" min="1" value="480">
                <button type="button" id="generate-button">Generate</button>
                <button type="button" id="save-button" disabled>Save to Library</button>
            </form>
            <div id="preview"></div>
        </div>
        <?php
    }
}

new Placeholder_Image_Generator();
