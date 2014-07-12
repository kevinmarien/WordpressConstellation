<?php
/*
Plugin Name: PostMeta
Description: Extension PostMeta
Version: 0.1
License: GPL
Author: Kevin Marien, Jonathan Esedji
*/
if ( !class_exists("PostMeta") )
{
    class PostMeta
    {

        public function install() {
            /*
            global $wpdb;
            $query = "CREATE TABLE ".$wpdb->prefix."collection (id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, nom VARCHAR(150) NOT NULL, description VARCHAR(255) NOT NULL, couleur VARCHAR(7) NOT NULL, photo VARCHAR(255) NOT NULL, type VARCHAR(150) NOT NULL, date DATE NOT NULL, visible BOOL NOT NULL) CHARACTER SET utf8 COLLATE utf8_general_ci";
            $wpdb->query($query);
            */
        }
        
        public function uninstall() {
            /*
            global $wpdb;
            $query = "DROP TABLE ".$wpdb->prefix."collection";
            $wpdb->query($query);
            */
        }
    }

    function PostMeta_menu() {
       add_options_page( 'PostMeta', 'PostMeta', 'manage_options', __FILE__, 'PostMeta_admin' );
    }

    function get_PostMeta($attrs){
        /*
        $meta = get_post_meta(get_the_ID());
        print_r($meta);
        echo "ok";
        */
        echo get_post_meta('post');
    }

    function PostMeta_admin() {
        global $title;
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( "Vous n'avez pas les permissions pour accéder à cette page." ) );
        }
        else{
            get_PostMeta('all');
        }
        
    }

    register_activation_hook(__FILE__, array('PostMeta','install'));
    register_deactivation_hook( __FILE__, array('PostMeta','uninstall'));

    add_action('admin_menu', 'PostMeta_menu');
    add_shortcode('PostMeta', 'get_PostMeta');
}
?>