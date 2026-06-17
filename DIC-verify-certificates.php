<?php
/*
 * Plugin Name:       DIC Verify Certificates
 * Plugin URI:        https://graceinfomedia.com
 * Description:       Verify your certificate at Dhanjal IELTS Classes.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Grace Info Media
 * Author URI:        https://graceinfomedia.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       dic-verify-certificates
 */
//  If accessed directly, abort
if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
}
 //[verify-certificate]
function verify_certificate_function( $atts ) {
	ob_start(); ?>
			<form id="verifyCert" action="" method="POST">
				<label for="fname">Enter your roll number:</label>
				<input type="text" id="rollnumber" name="rollnumber" placeholder="ex. DIC-7861301" required>
				<input type="hidden" name="certverify" value="https://deploy.graceinfomedia.com/dic-shahkot/verifycerts/">
				<input type="submit" value="Submit">
			</form>
		<?php
			$path = plugin_dir_path( __FILE__ ) . 'your-certificates';
			//echo $path;
			$dir_handle = @opendir($path) or die("Cannot open the damn file $path");
			//echo $dir_handle;

			while ($file = readdir($dir_handle)) {

				//get length of filename inc. extension
				$length_of_filename = strlen($file); 

				//strip all but last three characters from file name to get extension
				$ext = substr($file, -3, $length_of_filename); 

				if($ext == "jpg" ) {

				$TheLinkedFile = $path."/".$file;
				
					if ($_SERVER["REQUEST_METHOD"] == "POST") {
					  $certverify = htmlspecialchars($_POST['rollnumber']);
					  if (empty($certverify)) {
						//echo "Please enter your roll number";
						//break;
					  } else if($TheLinkedFile == $path . '/' . $certverify . '.jpg'){
						   //echo "Hello its matched";
						   echo '<img class="certimg" src=' . plugin_dir_url( __FILE__ ) . 'your-certificates/' . $certverify . '.jpg width="100%" height="100%">';
						   //echo plugin_dir_url( __FILE__ ).'your-certificates/' . $certverify;
						   //echo $TheLinkedFile;
						   //break;
					  }else {
						  //echo "It is not matched in our records.";
						  //echo '<p class="paragraph'.$i.'"></p>';
						  //break;
					  }
					}

					/*if(file_exists($TheLinkedFile)) {
						echo $TheLinkedFile.'<br><br>';
					} else {
						echo "nothing";
					}*/
				}

			}
			closedir($dir_handle);
	
	return ob_get_clean();
}
add_shortcode( 'verify_certificate', 'verify_certificate_function' );