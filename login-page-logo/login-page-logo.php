<?php
/*
Plugin Name: WP Login Logo
Description: Allows the administrator to change the default login logo on the wp-login.php page.
Version: 1.0
Author: Your Name
*/

function wp_login_logo_enqueue_scripts() {
    wp_enqueue_style( 'wp-login-logo', plugin_dir_url( __FILE__ ) . 'css/wp-login-logo.css' );
}
add_action( 'login_enqueue_scripts', 'wp_login_logo_enqueue_scripts' );

function wp_login_logo_admin_menu() {
    add_options_page( 'WP Login Logo', 'WP Login Logo', 'manage_options', 'wp-login-logo', 'wp_login_logo_options_page' );
}
add_action( 'admin_menu', 'wp_login_logo_admin_menu' );

function wp_login_logo_options_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( isset( $_POST['wp_login_logo_nonce'] ) && wp_verify_nonce( $_POST['wp_login_logo_nonce'], 'wp_login_logo_options' ) ) {
        update_option( 'wp_login_logo', sanitize_text_field( $_POST['wp_login_logo'] ) );
    }

    $wp_login_logo = get_option( 'wp_login_logo' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field( 'wp_login_logo_options', 'wp_login_logo_nonce' ); ?>
            <p>
                <label for="wp_login_logo"><?php esc_html_e( 'Login Logo URL:', 'wp-login-logo' ); ?></label>
                <br />
                <input type="text" id="wp_login_logo" name="wp_login_logo" value="<?php echo esc_url( $wp_login_logo ); ?>" class="regular-text" />
            </p>
            <p>
                <input type="submit" value="<?php esc_attr_e( 'Save Changes', 'wp-login-logo' ); ?>" class="button button-primary" />
            </p>
        </form>
        <?php if ( $wp_login_logo ) : ?>
            <p> 
                <img src="<?php echo esc_url( $wp_login_logo ); ?>" alt="" style="max-width:100%;" />
            </p>
        <?php endif; ?>
    </div>
    <?php
}
