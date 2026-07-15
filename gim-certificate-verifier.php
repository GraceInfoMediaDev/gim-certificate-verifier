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

            // Inject styles into the page header
            add_action( 'wp_head', array( $this, 'inject_plugin_styles' ) );
        }

        /*
         * Main shortcode callback function.
         */
        public function render_certificate_verifier() {
            ob_start();

            // Wrap everything inside a dedicated class wrapper for clean targetting
            echo '<div class="cert-verifier-container">';
            
            $this->render_form();

            if ( isset( $_POST['cert_nonce'] ) && wp_verify_nonce( $_POST['cert_nonce'], 'verify_cert_action' ) ) {
                echo '<div class="cert-response">';
                $this->process_verification();
                echo '</div>';
            }

            echo '</div>';

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
            // Directly check if the specific file exists
            if ( file_exists( $target_file_path ) ) {
                echo '<div class="cert-success">';
                echo '<img class="certimg" src="' . esc_url( $target_file_url ) . '" width="100%" height="100%" alt="Verified Certificate">';
                echo '</div>';
            } else {
                echo '<p class="cert-error">It is not matched in our records.</p>';
            }
        }

        /**
         * Injects responsive, modern CSS into the WordPress header.
         */
        public function inject_plugin_styles() {
            ?>
            <style>
                .cert-verifier-container {
                    max-width: 800px;
                    margin: 2rem auto;
                    padding: 2rem;
                    background: #ffffff;
                    border-radius: 8px;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                }
                .cert-verifier-container form {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                }
                .cert-verifier-container label {
                    font-weight: 600;
                    color: #333333;
                    font-size: 1rem;
                    margin-bottom: 4px;
                }
                .cert-verifier-container input[type="text"] {
                    width: 100%;
                    padding: 12px;
                    border: 1px solid #cccccc;
                    border-radius: 6px;
                    font-size: 1rem;
                    transition: border-color 0.2s ease, box-shadow 0.2s ease;
                    box-sizing: border-box;
                }
                .cert-verifier-container input[type="text"]:focus {
                    border-color: #0073aa;
                    box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.2);
                    outline: none;
                }
                .cert-verifier-container input[type="submit"] {
                    background-color: #0073aa;
                    color: #ffffff;
                    border: none;
                    padding: 12px 20px;
                    font-size: 1rem;
                    font-weight: 600;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: background-color 0.2s ease;
                }
                .cert-verifier-container input[type="submit"]:hover {
                    background-color: #005177;
                }
                .cert-response {
                    margin-top: 1.5rem;
                    border-top: 1px solid #eeeeee;
                    padding-top: 1.5rem;
                }
                .cert-error {
                    background-color: #fff0f0;
                    border-left: 4px solid #d9381e;
                    color: #d9381e;
                    padding: 12px;
                    border-radius: 0 6px 6px 0;
                    font-weight: 500;
                    margin: 0;
                }
                .cert-image-wrap {
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    border-radius: 6px;
                    overflow: hidden;
                    display: block;
                }
                .certimg {
                    display: block;
                    width: 100%;
                    height: auto;
                }
            </style>
            <?php
        }
    }


    // Initialize the plugin
    new GIM_Certificate_Verifier();
}