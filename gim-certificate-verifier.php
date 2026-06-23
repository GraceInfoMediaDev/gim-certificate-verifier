<?php
/*
 * Plugin Name:       GIM Certificate Verifier
 * Plugin URI:        https://graceinfomedia.com
 * Description:       Allow schools and colleges to verify issued certificates on their website.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Grace Info Media
 * Author URI:        https://graceinfomedia.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gim-certificate-verifier
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
// Define plugin constants
define( 'GIM_CV_VERSION', '1.0.0' );
define( 'GIM_CV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GIM_CV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( 'GIM_Certificate_Verifier' ) ) {
    class GIM_Certificate_Verifier {
        private string $cert_dir_path;
        private string $cert_dir_url;
        public function __construct() {
            $this->cert_dir_path = GIM_CV_PLUGIN_DIR . '/your-certificates/';
            $this->cert_dir_url = GIM_CV_PLUGIN_URL . '/your-certificates/';

            // Register shortcode
            add_shortcode( 'gim_certificate_verifier', array( $this, 'render_certificate_verifier' ) );
        }

        /*
         * Main shortcode callback function.
         */
        public function render_certificate_verifier() {
            ob_start();

            // 1. Render the submission form
            $this->render_form();

            // 2. Process submission if it exists
            if ( isset( $_POST['cert_nonce'] ) && wp_verify_nonce( $_POST['cert_nonce'], 'verify_cert_action' ) ) {
                $this->process_verification();
            }

            return ob_get_clean();
        }

        /**
         * Renders the HTML input form.
         */
        private function render_form() {
            ?>
            <form id="verifyCert" action="" method="POST">
                <?php wp_nonce_field( 'verify_cert_action', 'cert_nonce' ); ?>
                <label for="rollnumber">Enter your roll number:</label>
                <input type="text" id="rollnumber" name="rollnumber" placeholder="ex. DIC-7861301" required>
                <input type="submit" value="Submit">
            </form>
            <?php
        }

        /**
         * Validates input and verifies if the certificate exists.
         */
        private function process_verification() {
            if ( empty( $_POST['rollnumber'] ) ) {
                echo '<p class="cert-error">Please enter your roll number.</p>';
                return;
            }

            // Sanitize the input to prevent XSS/malicious input
            $roll_number = sanitize_text_field( $_POST['rollnumber'] );
            $file_name   = $roll_number . '.jpg';
            
            $target_file_path = $this->cert_dir_path . $file_name;
            $target_file_url  = $this->cert_dir_url . $file_name;
            echo '<p style="color:red;">Searching for file at: ' . esc_html( $target_file_path ) . '</p>';
            // Directly check if the specific file exists
            if ( file_exists( $target_file_path ) ) {
                echo '<div class="cert-success">';
                echo '<img class="certimg" src="' . esc_url( $target_file_url ) . '" width="100%" height="100%" alt="Verified Certificate">';
                echo '</div>';
            } else {
                echo '<p class="cert-error">It is not matched in our records.</p>';
            }
        }
    }


    // Initialize the plugin
    new GIM_Certificate_Verifier();
}