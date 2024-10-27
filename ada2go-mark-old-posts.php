<?php
/*
Plugin Name: Ada2go - Mark your old Articles
Description: (DE) Mit diesem Plugin werden alte Beitr&auml;ge automatisch mit einem Hinweis versehen. (EN) This plugin marks posts that have not been updated for a long time. In the standard settings it is 365 days. It is not about the time of publication, its calculated by the time of the last update. Go to the Settings to change the Time by Secounds. A list of all marked posts is displayed in the settings.
Version: 1.2
Author: Heiko von ada2go.de
Author URI: https://ada2go.de/
Text Domain: ada2go-mark-old-posts
*/

defined( 'ABSPATH' ) or die( 'Huuuuuuh?' );

/** 
 * ADD FUNCTIONS
*/

/** 
 * FUNCTION a2g_mop_seconds_to_words
 * Calculate the Output
*/
function a2g_mop_seconds_to_words($seconds)
          {
              $a2g_ret = "";
              /*** get the days ***/
              $days = intval(intval($seconds) / (3600*24));
              if($days> 0)
              {
                  $a2g_ret .= "$days ";
              }
              /*** get the hours ***/
              $hours = (intval($seconds) / 3600) % 24;
              if($hours > 0)
              {
                  $a2g_ret .= "$hours ";
              }
              /*** get the minutes ***/
              $minutes = (intval($seconds) / 60) % 60;
              if($minutes > 0)
              {
                  $a2g_ret .= "$minutes ";
              }
              /*** get the seconds ***/
              $seconds = intval($seconds) % 60;
              if ($seconds > 0) {
                  $a2g_ret .= "$seconds";
              }
              return $a2g_ret;
          }
          
/** 
 * FUNCTION a2g_mop_old_postings
 * Add the Mark to Content
*/
function a2g_mop_old_postings( $content ) 
          {
            global $wpdb;	
          // mark the single article only
          // built output on bottom
          if ( is_single() ) {
          $a2g_post_date = get_the_modified_time('U'); // the date
          $a2g_actually_date = time(); // current timestamp
          $a2g_math = $a2g_actually_date-$a2g_post_date; // actually timestamp - posttime
        	$a2g_get_m_data	=	$wpdb-> get_row("SELECT *  FROM ".$wpdb->prefix."markoldposts_settings WHERE id = 1"); // select the settings from DB	
        	$a2g_get_display_message = $a2g_get_m_data->message; // get the message
        	$a2g_get_secounds = $a2g_get_m_data->name; // get the secounds
        	$a2g_make_time_readable = a2g_mop_seconds_to_words($a2g_get_secounds);  
        	$a2g_get_display_message = str_replace("%sec%", " ".$a2g_make_time_readable." ", $a2g_get_display_message); // searching for placeholder an replace

          // check if post older than the secs in settings
          // true = get mark
          if($a2g_math>=$a2g_get_secounds) 
                  {
                    $a2g_mop_old_postings = '
                      <footer class="older-post-hint" >
                          <div class="older-post-text">'.esc_html($a2g_get_display_message).'</div>
                      </footer>';
                  } 
 
  }
  // return content and hint
  return $a2g_mop_old_postings.$content;
}

/** 
 * Add the Submenu to Settings
*/
function a2g_mop_options_submenu() {
  add_submenu_page(
        'options-general.php',
        'Mark Older Posts - Settings',
        'Mark Older Posts',
        'administrator',
        'a2g_mop_settings',
        'a2g_mop_settings_page' );
}

/** 
 * Require settings_page.php
*/
function a2g_mop_settings_page() { 
require "settings_page.php";
}

/** 
 * DEACTIVATION
 * Delete Database Table
 * This Function is actualy not used
*/


function a2g_mop_uninstall() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'markoldposts_settings';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
}

/** 
 * ADD Filter
*/
add_filter( 'the_content', 'a2g_mop_old_postings' );

/** 
* LOAD individual CSS File
*/
function a2g_mop_css() {
  wp_enqueue_style( 'ada2go-mark-old-posts', plugin_dir_url( __FILE__ ) . 'ada2go-mark-old-posts.css' );  
}
add_action( 'wp_enqueue_scripts', 'a2g_mop_css' );  
add_action( 'admin_enqueue_scripts', 'a2g_mop_css');

/** 
* ADD submenu to Settings
*/
add_action("admin_menu", "a2g_mop_options_submenu");

/** 
 * ACTIVATION
 * Create Database Table and Insert a Value
*/
function a2g_mop_activate() {
        global $wpdb;
        $table = $wpdb->prefix . 'markoldposts_settings';
        $charset = $wpdb->get_charset_collate();
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
		    message varchar(255),
        PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
		// Fill Database with default inputs
		$wpdb->insert($table, array('id' => 1, 'name' => 15552000, 'message' => __('Dieser Beitrag wurde vor &uuml;ber 6 Monaten aktualisiert. Gerade die rechtlichen Themen k&ouml;nnten nicht mehr aktuell sein.', 'ada2go-mark-old-posts')) ); 
}   

// hook on activation
register_activation_hook( __FILE__, 'a2g_mop_activate' );
register_uninstall_hook( __FILE__, 'a2g_mop_uninstall' );
