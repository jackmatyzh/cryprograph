<?php
/**
 * Plugin Name: Cryprograph
 * Description: The plugin that shows cryptocurrency change rate. Just use a shortcode [crypto] on any page you want to display a plugin to.
 * Version: 1.0
 * Author: Yevhen Matiazh
 */
    
 
 //create table for a change rate
global $jal_db_version;
$echange_rate_version = '1.0';

function echange_rate() {
	global $wpdb;
	global $echange_rate_version;

	$table_name = $wpdb->prefix . 'echange_rate11';
	
	$charset_collate = $wpdb->get_charset_collate();
    $wp_track_table = $table_prefix . "$tblname ";
    // Check to see if the table exists already, if not, then create it

        if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table) 
        {
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                usdBTC decimal(32,9) NOT NULL,
                usdETH decimal(32,9) NOT NULL,
                usdUSDT decimal(32,9) NOT NULL,
                usdBNB decimal(32,9) NOT NULL,
                usdADA decimal(32,9) NOT NULL,
                usdDOGE decimal(32,9) NOT NULL,
                usdXRP decimal(32,9) NOT NULL,
                usdUSDC decimal(32,9) NOT NULL,
                usdDOT decimal(32,9) NOT NULL,
                usdUNI decimal(32,9) NOT NULL,
                url varchar(55) DEFAULT '' NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            add_option( 'echange_rate_version', $echange_rate_version );
    }
}
register_activation_hook( __FILE__, 'echange_rate' );

//create table for history log
function echange_history() {
	global $wpdb;
	global $echange_history_version;

	$table_name = $wpdb->prefix . 'history_rate11';
	
	$charset_collate = $wpdb->get_charset_collate();
    $wp_track_table = $table_prefix . "$tblname ";
    // Check to see if the table exists already, if not, then create it

        if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table) 
        {
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                amount decimal(32,9) NOT NULL,
                curr1 text,
                curr2 text,
                resultFull decimal(32,9) NOT NULL,
              
                
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            add_option( 'echange_history_version', $echange_history_version );
    }
}
register_activation_hook( __FILE__, 'echange_history' );

 // The shortcode function
function wpb_demo_crypto() { 
 
    include('change.html');
     
    }
    // Register shortcode to render plugin to a front page
    add_shortcode('crypto', 'wpb_demo_crypto'); 
    
    //call an API request every 5 min
    add_filter( 'cron_schedules', 'wpshout_add_cron_interval' );
function wpshout_add_cron_interval( $schedules ) {
    $schedules['everyminute'] = array(
            'interval'  => 5*60, // time in seconds
            'display'   => 'Every Minute'
    );
    return $schedules;
}
    register_activation_hook( __FILE__, 'wpshout_plugin_activation' );

    function wpshout_plugin_activation() {
        if ( ! wp_next_scheduled( 'min5api' ) ) {
          wp_schedule_event( time(), 'everyminute', 'min5api' );
        }
    }
    register_deactivation_hook( __FILE__, 'min5api' );


    function wpshout_plugin_deactivation() {
        wp_clear_scheduled_hook( 'min5api' );
    }
    
    add_action('min5api','min5api');
    function min5api() {
        
            $json = file_get_contents('https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?CMC_PRO_API_KEY=cc4cfde8-64f1-4af8-85de-3c87d541cbcf');
        $obj = json_decode($json);
    
        // Store data in database
        global $wpdb;
        $table_name = $wpdb->prefix . 'echange_rate11';
        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time( 'mysql' ),
                'usdBTC' => $obj->data[0]->quote->USD->price,
                'usdETH' => $obj->data[1]->quote->USD->price,
                'usdUSDT' => $obj->data[2]->quote->USD->price,
                'usdBNB' => $obj->data[3]->quote->USD->price,
                'usdADA' => $obj->data[4]->quote->USD->price,
                'usdDOGE' => $obj->data[5]->quote->USD->price,
                'usdXRP' => $obj->data[6]->quote->USD->price,
                'usdUSDC' => $obj->data[7]->quote->USD->price,
                'usdDOT' => $obj->data[8]->quote->USD->price,
                'usdUNI' => $obj->data[9]->quote->USD->price
            )
        );
        };
    
//render history log to a front page
    add_action( 'wp_ajax_hello', 'show_history' );
    add_action( 'wp_ajax_nopriv_hello', 'show_history' );
    function show_history() {
        global $wpdb;
        $gett = $wpdb->get_results(" SELECT * FROM ".$wpdb->prefix."history_rate11 ORDER BY ID DESC LIMIT 10");
       
        echo ('<div id="history-log-main">');
        echo ('<h3>History log</h3>');
        
        echo ('<div id="history-log">');

        for ($num = 0; $num <= 9; $num++) {
        echo (' <span>');
        print_r($gett[$num]->curr1);echo (' ');
        print_r(round($gett[$num]->amount, 4)); 
        echo (' to '); print_r($gett[$num]->curr2);echo (' = '); 
        print_r(round($gett[$num]->resultFull, 4));echo (' </br> '); echo ('</span>');
        }
        
        echo ('</div>');
        echo ('</div>');
        

        wp_die();
    }
    
    
    
    add_action( 'wp_enqueue_scripts', 'my_assets' );
    function my_assets() {
        wp_enqueue_script( 'custom', plugins_url( 'myScript.js', __FILE__ ), array( 'jquery' ) );
        
        wp_localize_script( 'custom', 'myPlugin', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'name'    => wp_get_current_user()->display_name
        ) );
    }

    add_action( 'wp_enqueue_scripts', 'get_style_css' );
    function get_style_css() {
        wp_enqueue_style( 'style-name', plugins_url( '/css/style.css', __FILE__ ) );
    }
    