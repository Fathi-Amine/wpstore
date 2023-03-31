<?php
/**
 * Plugin Name
 *
 * @package           PluginPackage
 * @author            amineFathi
 * @copyright         2019 Your Name or Company Name
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Amine's's plugin
 * Plugin URI:        https://fathi.me
 * Description:       this plugin is for adding a conact form
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            amineFathi
 * Author URI:        https://sakim.me
 * Text Domain:       plugin-slug
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/my-plugin/
 */

 defined('ABSPATH') or die('access denied');


 class aminesPlugin 
 {
    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', [$this,'bootstrap_css']);
        add_action( 'wp_head', [$this,'formCapture']);
        add_action('phpmailer_init',[$this, 'mailtrap']);
        
    }

    public function activation()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . "contactus";
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(55) NOT NULL,
        useremail varchar(55) DEFAULT 'def' NOT NULL,
        subject varchar(55) DEFAULT '' NOT NULL,
        message text DEFAULT '' NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        flush_rewrite_rules();

    }

    public function deactivation()
    {
        flush_rewrite_rules();

    }

    public function bootstrap_css() {
        wp_enqueue_style( 'bootstrap_css', 
                          'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css', 
                          array(), 
                          '4.1.3'
                          ); 
    }

    public function formCapture(){
        if(isset($_POST['formSubmit'])){
            if (empty($_POST["name"])) {
                $_SESSION['name-error'] = "Name is required";
                
            }
            if (empty($_POST["email"])) {
                $_SESSION['email-error'] = "Email is required";
                
            }
            if (empty($_POST["sujet"])) {
                $_SESSION['sujet-error'] = "Subject is required";
                
            }
            if (empty($_POST["message"])) {
                $_SESSION['message-error'] = "Message is required";
                
            }
            global $wpdb;
            $table = $wpdb->prefix.'contactus';
            $data = array('name' => $_POST['name'], 'useremail' =>$_POST['email'],'subject'=>$_POST['sujet'],'message'=>$_POST['message']);
            // $format = array('%s','%d');
            $wpdb->insert($table,$data);
            $to = "wejay62533@jthoven.com";
            $subject = $_POST['sujet'];
            $message="a message from ".$_POST['name'].'--'.$_POST['message'];
            wp_mail($to, $subject, $message);
            echo "<meta http-equiv='refresh' content='0'>";
        }

    }
    public function mailtrap($phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 2525;
        $phpmailer->Username = '5eb86bc3df7222';
        $phpmailer->Password = '41c51d96a8b5dd';
      }
      
    public function form(){
        $content = '';
        $content .= '<form method="GET">';
        $content .= '<div class="mb-3">';
        $content .=  '<label for="exampleInputEmail1" class="form-label">Nom du visiteur</label>';
        $content .=  '<input type="text" name="Name" class="form-control" required>';
        if(isset($_SESSION['name-error'])){

            $content .='<div class="text-danger">'.$_SESSION['name-error'].'</div>';
           unset($_SESSION['name-error']);
        }
        $content .=  '</div>';
        
        $content .='<div class="mb-3">';
        $content .=  '<label for="exampleInputPassword1" class="form-label">Email</label>';
        $content .=  '<input type="email" name="Email" class="form-control" required>';

        if(isset($_SESSION['email-error'])){

            $content .='<div class="text-danger">'.$_SESSION['email-error'].'</div>';
        }
        $content.='</div>';

        $content .='<div class="mb-3">';
        $content .=  '<label for="exampleInputPassword" class="form-label">Sujet</label>';
        $content .=  '<input type="text" name="Sujet" class="form-control" required>';

        if(isset($_SESSION['sujet-error'])){

            $content .='<div class="text-danger">'.$_SESSION['sujet-error'].'</div>';
        }
        $content.='</div>';

        $content .='<div class="mb-3">';
        $content .=  '<label for="exampleInputPassword1" class="form-label">Message</label>';
        $content .=  '<input type="text" name="Message" class="form-control" required>';

        if(isset($_SESSION['message-error'])){

            $content .='<div class="text-danger">'.$_SESSION['message-error'].'</div>';
        }
        $content.='</div>';
        
        $content .='<button type="submit" name="FormSubmit" class="btn btn-primary">Submit</button></form>';
      

        return $content;
    }



 }


 $aminesPlugin = new aminesPlugin();


//  activation
register_activation_hook(
	__FILE__,
	[$aminesPlugin,'activation']
);

//  deactivation

register_deactivation_hook(
	__FILE__,
    [$aminesPlugin,'deactivation']

);

// uninstall

register_uninstall_hook(
	__FILE__,
    [$aminesPlugin,'uninstallation']
);

add_shortcode('aminesForm',[$aminesPlugin,'form']);


