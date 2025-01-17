<?php
/**

 * Plugin Name: HelloBox
 * Plugin URI: http://gethellobox.com
 * Description: Easily add your HelloBox to your WordPress site!
 * Version: 0.2
 * Author: Per Schmitz
 * Author URI: http://perschmitz.com
 * License: GPL2

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class HelloBoxForWordPress {
    var $longname = "HelloBox for WordPress";
    var $shortname = "HelloBox";
    var $namespace = 'hellobox-for-wordpress';
    var $version = '0.2';

    var $defaults = array(
        'hellobox_code' => "",
        'load_hellobox_in' => 'footer'
    );

    function __construct() {
        $this->url_path = WP_PLUGIN_URL . "/" . plugin_basename( dirname( __FILE__ ) );

        if( isset( $_SERVER['HTTPS'] ) ) {
            if( (boolean) $_SERVER['HTTPS'] === true ) {
                $this->url_path = str_replace( 'http://', 'https://', $this->url_path );
            }
        }

        $this->option_name = '_' . $this->namespace . '--options';

        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );

        if( is_admin() ) {

            wp_register_style( $this->namespace, $this->url_path . '/' . $this->namespace . '.css', array(), $this->version );
            wp_enqueue_style( $this->namespace );

            wp_register_script( $this->namespace, $this->url_path . '/' . $this->namespace . '.js', array( 'jquery' ), $this->version );
            wp_enqueue_script( $this->namespace );

        } else {

        	if( $this->get_option( 'load_hellobox_in' ) == 'header' ){
		        add_action( 'wp_head', array( &$this, 'hellobox_print_script' ) );
        	} else {
			    if( function_exists( 'wp_print_footer_scripts' ) ) {
			        add_action( 'wp_print_footer_scripts', array( &$this, 'hellobox_print_script' ) );
			    } else {
			        add_action( 'wp_footer', array( &$this, 'hellobox_print_script' ) );
			    }
        	}

        }
    }

	function hellobox_print_script () {
	    $hellobox_code = $this->get_option( 'hellobox_code' );

		if( !empty( $hellobox_code ) ) {
			$hellobox_code = html_entity_decode( $hellobox_code );

			if( $this->get_option( 'load_hellobox_in' ) == 'header' ) {
				$output = preg_replace( "/<noscript>(.*)<\/noscript>/ism", "", $hellobox_code );
			} else {
				$output = $hellobox_code;
			}

			echo "\n" . $output;
		}
	}

    function admin_menu() {
        add_menu_page( $this->shortname, $this->shortname, 2, basename( __FILE__ ), array( &$this, 'admin_options_page' ), ( $this->url_path.'/images/icon.png' ) );
    }
    function admin_options_page() {
        if( !current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page' );
        }

        if( isset( $_POST ) && !empty( $_POST ) ) {
            if( wp_verify_nonce( $_REQUEST[$this->namespace . '_update_wpnonce'], $this->namespace . '_options' ) ) {
                $data = array();
                foreach( $_POST as $key => $val ) {
                    $data[$key] = $this->sanitize_data( $val );
                }

                switch( $data['form_action'] ) {
                    case "update_options":
                        $options = array(
                            'hellobox_code' => (string) $data['hellobox_code'],
							'load_hellobox_in' => (string) $data['load_hellobox_in']
                        );

                        update_option( $this->option_name, $options );
                        $this->options = get_option( $this->option_name );
                    break;
                }
            }
        }

        $page_title = $this->longname . ' Options';
        $namespace = $this->namespace;
        $options = $this->options;
        $defaults = $this->defaults;
        $plugin_path = $this->url_path;

        foreach( $this->defaults as $name => $default_value ) {
            $$name = $this->get_option( $name );
        }
        include( dirname( __FILE__ ) . '/views/options.php' );
    }

    private function get_option( $option_name ) {
        // Load option values if they haven't been loaded already
        if( !isset( $this->options ) || empty( $this->options ) ) {
            $this->options = get_option( $this->option_name, $this->defaults );
        }

        if( isset( $this->options[$option_name] ) ) {
            return $this->options[$option_name];    // Return user's specified option value
        } elseif( isset( $this->defaults[$option_name] ) ) {
            return $this->defaults[$option_name];   // Return default option value
        }
        return false;
    }

    private function sanitize_data( $str="" ) {
        if ( !function_exists( 'wp_kses' ) ) {
            require_once( ABSPATH . 'wp-includes/kses.php' );
        }
        global $allowedposttags;
        global $allowedprotocols;

        if ( is_string( $str ) ) {
            $str = htmlentities( stripslashes( $str ), ENT_QUOTES, 'UTF-8' );
        }

        $str = wp_kses( $str, $allowedposttags, $allowedprotocols );

        return $str;
    }

}

add_action( 'init', 'HelloBoxForWordPress' );
function HelloBoxForWordPress() {
    global $HelloBoxForWordPress;

    $HelloBoxForWordPress = new HelloBoxForWordPress();
}
?>
