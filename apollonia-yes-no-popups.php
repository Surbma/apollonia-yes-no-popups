<?php

/*
Plugin Name: Apollonia - Yes/No Popups
Description: Shows popups with Yes/No options

Version: 1.0.0

Author: Surbma
Author URI: http://apollonia.com/

License: GPLv2

Text Domain: apollonia-yes-no-popupss
Domain Path: /languages/
*/

// Prevent direct access to the plugin
if ( !defined( 'ABSPATH' ) ) exit( 'Good try! :)' );

define( 'APOLLONIA_YES_NO_POPUPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'APOLLONIA_YES_NO_POPUPS_PLUGIN_URL', plugins_url( '', __FILE__ ) );

// Localization
add_action( 'plugins_loaded', 'apollonia_yes_no_popups_init' );
function apollonia_yes_no_popups_init() {
	load_plugin_textdomain( 'apollonia-yes-no-popups', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}

// Include files
if ( is_admin() ) {
	include_once( APOLLONIA_YES_NO_POPUPS_PLUGIN_DIR . '/lib/admin.php' );
}

add_action( 'wp_enqueue_scripts', 'apollonia_yes_no_popups_enqueue_scripts', 999 );
function apollonia_yes_no_popups_enqueue_scripts() {
	wp_enqueue_script( 'apollonia-yes-no-popups-scripts', plugins_url( '', __FILE__ ) . '/js/scripts-min.js', array( 'jquery' ), '2.27.1', true );
	wp_enqueue_style( 'apollonia-yes-no-popups-styles', plugins_url( '', __FILE__ ) . '/css/styles.css', false, '2.27.1' );
}

add_action( 'wp_footer', 'apollonia_yes_no_popups_show' );
function apollonia_yes_no_popups_show() {
	$options = get_option( 'apollonia_yes_no_popups_fields' );

	if( $options['popupshoweverywhere'] == 1 ) {
		add_action( 'wp_footer', 'apollonia_yes_no_popups_block', 999 );
	}
	else {
		if( $options['popupshowfrontpage'] == 1 && is_front_page() ) {
			add_action( 'wp_footer', 'apollonia_yes_no_popups_block', 999 );
		}
		if( $options['popupshowblog'] == 1 && is_home() ) {
			add_action( 'wp_footer', 'apollonia_yes_no_popups_block', 999 );
		}
		if( $options['popupshowarchive'] == 1 && is_archive() ) {
			add_action( 'wp_footer', 'apollonia_yes_no_popups_block', 999 );
		}
		if( $options['popupshowallposts'] == 1 && $options['popupshowposts'] == '' && is_singular( 'post' ) ) {
			add_action( 'wp_footer', 'apollonia_yes_no_popups_block', 999 );
		}
		if( $options['popupshowallpages'] == 1 && $options['popupshowpages'] == '' && is_page() ) {
			add_action( 'wp_footer', 'apollonia_yes_no_popups_block', 999 );
		}
		$includeposttypes = $options['popupshowposttypes'] ? explode( ',', $options['popupshowposttypes'] ) : '';
		if( $options['popupshowposttypes'] != '' && $options['popupshowposts'] == '' && is_singular( $includeposttypes ) ) {
			add_action( 'wp_footer', 'apollonia_yes_no_popups_block', 999 );
		}
		$includeposts = $options['popupshowposts'] ? explode( ',', $options['popupshowposts'] ) : '';
		if( $options['popupshowposts'] != '' && is_single( $includeposts ) ) {
			add_action( 'wp_footer', 'apollonia_yes_no_popups_block', 999 );
		}
		$includepages = $options['popupshowpages'] ? explode( ',', $options['popupshowpages'] ) : '';
		if( $options['popupshowpages'] != '' && is_page( $includepages ) ) {
			add_action( 'wp_footer', 'apollonia_yes_no_popups_block', 999 );
		}
	}
}

function apollonia_yes_no_popups_block() {
	if( !isset( $_COOKIE['apollonia-yes-no-popups'] ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG == true ) ) {
		$options = get_option( 'apollonia_yes_no_popups_fields' );

		if ( !isset( $options['popupcookiedays'] ) || $options['popupcookiedays'] == '' )
			$options['popupcookiedays'] = 1;
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$.UIkit.modal(('#apollonia-yes-no-popups'), {center: true,keyboard: false,bgclose: false}).show();
		});
	</script>
	<div id="apollonia-yes-no-popups" class="uk-modal">
        <div class="uk-modal-dialog">
			<div class="uk-modal-header">
				<h2><?php echo esc_attr_e( $options['popuptitle'] ); ?></h2>
			</div>
			<div class="uk-modal-content"><?php echo stripslashes( $options['popuptext'] ); ?></div>
			<div class="uk-modal-footer uk-text-right">
				<button id="button1" type="button" class="uk-button uk-button-large uk-button-<?php echo esc_attr_e( $options['popupbutton1style'] ); ?><?php if( $options['popupbuttonoptions'] != 'button-1-redirect' ) echo ' uk-modal-close'; ?>">
					<?php echo esc_attr_e( $options['popupbutton1text'] ); ?>
				</button>
				<button id="button2" type="button" class="uk-button uk-button-large uk-button-<?php echo esc_attr_e( $options['popupbutton2style'] ); ?><?php if( $options['popupbuttonoptions'] == 'button-1-redirect' ) echo ' uk-modal-close'; ?>">
					<?php echo esc_attr_e( $options['popupbutton2text'] ); ?>
				</button>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		function setCookie() {
		    var d = new Date();
		    d.setTime(d.getTime() + (<?php echo esc_attr_e( $options['popupcookiedays'] ); ?>*24*60*60*1000));
		    var expires = "expires="+ d.toUTCString();
		    document.cookie = "apollonia-yes-no-popups=yes;" + expires + ";path=/";
		}
		<?php if( $options['popupbuttonoptions'] != 'button-1-redirect' ) { ?>
	    	document.getElementById("button1").onclick = function () {
				setCookie();
	    	};
	    	document.getElementById("button2").onclick = function () {
	        	location.href = "<?php echo esc_attr_e( $options['popupbuttonurl'] ); ?>";
	    	};
		<?php } else { ?>
	    	document.getElementById("button1").onclick = function () {
	        	location.href = "<?php echo esc_attr_e( $options['popupbuttonurl'] ); ?>";
	    	};
	    	document.getElementById("button2").onclick = function () {
				setCookie();
	    	};
		<?php } ?>
	</script>
	<?php
	}
}