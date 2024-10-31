<?php 

/*
Plugin Name: RBS Remote Dashboard Welcome
Plugin URL: https://roadbearstudios.com/plugins/rbs-remote-dashboard-welcome
Description: **DEPRECATED** Load a Remote Dashboard Welcome page from a central server to show to your users on their own website.
Version: 1.1.5
Text Domain: rbs-remote-dashboard-welcome
Domain Path: /languages/
Author: Roadbear Studios - Rene Diekstra
Author URI: https://roadbearstudios.com
License: GPLv2
*/

//Only load this file as a plugin
if ( ! function_exists( 'add_action' ) ) {
        exit;
}

add_action( 'admin_init', 'rrdw_load_textdomain' );

/**
 * Load plugin textdomain.
 */
function rrdw_load_textdomain() {
	load_plugin_textdomain( 'rbs-remote-dashboard-welcome', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

$options = get_option( 'rrdw_settings' );

function rrdw_custom_dashboard() {
	$iframe_url = rrdw_build_iframe_url();

?>
<iframe id="dash-iframe" frameborder="0" src="<?php echo $iframe_url; ?>" width="100%" height="500px"></iframe>

<?php

}

function rrdw_build_iframe_url() {
	$req_hostname = get_site_url();
	$options = get_option( 'rrdw_settings' );
	
	$iframe_url = esc_url(add_query_arg( 'reqhost', urlencode($req_hostname), $options['rrdw_remote_dashboard_url'] ));

	return $iframe_url;
}

//Only add the javascript to the admin dashboard page
function rrdw_enqueue_admin_js( $page ) {
	$options = get_option( 'rrdw_settings' );
	
	$iframe_url = rrdw_build_iframe_url();
	
	//Get the domain part of the url
	$url_components = parse_url($iframe_url);
	$origin = $url_components['scheme'] ."://". $url_components['host'];
	
	if($page == 'index.php') {
		wp_enqueue_script( 'rrdw_functions', plugin_dir_url( __FILE__ ) . 'js/rrdw_functions.js', array(), '1.0' );
		wp_localize_script( 'rrdw_functions', 'rrdwObject', array(
				'origin' => $origin,
				'remote_url' => $iframe_url,
		) );
	}
}


if( $options['rrdw_remote_dashboard_url'] != '') { 

	//Remove the default WP welcome panel
	remove_action('welcome_panel', 'wp_welcome_panel');

	//Add our own dashboard
	add_action( 'welcome_panel', 'rrdw_custom_dashboard' );
	
	add_action( 'admin_enqueue_scripts', 'rrdw_enqueue_admin_js' );
}

function rrdw_add_admin_menu(  ) { 
	add_submenu_page( 'options-general.php', 'Remote Dashboard Welcome', 'Remote Dashboard Welcome', 'manage_options', 'remote_dashboard_welcome', 'rrdw_options_page' );
}

add_action( 'admin_menu', 'rrdw_add_admin_menu' );

function rrdw_settings_init(  ) { 

	register_setting( 'pluginPage', 'rrdw_settings' );

	add_settings_section(
		'rrdw_pluginPage_section', 
		__('Remote Dashboard Welcome Settings', 'rbs-remote-dashboard-welcome' ),
		'rrdw_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'rrdw_remote_dashboard_url',
		__('Url to remote dashboard page:' , 'rbs-remote-dashboard-welcome' ),
		'rrdw_remote_dashboard_url_render', 
		'pluginPage', 
		'rrdw_pluginPage_section' 
	);
}

add_action( 'admin_init', 'rrdw_settings_init' );

function rrdw_remote_dashboard_url_render(  ) { 

	$options = get_option( 'rrdw_settings' );
	?>
	<input type='text' size='50' name='rrdw_settings[rrdw_remote_dashboard_url]' value='<?php echo $options['rrdw_remote_dashboard_url']; ?>'>
	<?php

}

function rrdw_add_action_plugin($actions, $plugin_file) {
	static $plugin;

	if (! isset ( $plugin ))
		$plugin = plugin_basename ( __FILE__ );
		if ($plugin == $plugin_file) {

			$settings = array (
					'settings' => '<a href="options-general.php?page=remote_dashboard_welcome">' . __( 'Settings', 'rbs-remote-dashboard-welcome' ) . '</a>'
			);
			$site_link = array (
					'support' => '<a href="https://roadbearstudios.com/plugins/rbs-remote-dashboard-welcome" target="_blank">' . __( 'Support', 'rbs-remote-dashboard-welcome' ) . '</a>'
			);

			$actions = array_merge ( $settings, $actions );
			$actions = array_merge ( $site_link, $actions );
		}

		return $actions;
}

add_filter ( 'plugin_action_links', 'rrdw_add_action_plugin', 10, 5 );

function rrdw_options_page(  ) { 

	?>
		<div class="wrap">
		<h1><?php __( 'Remote Dashboard Welcome Settings', 'rbs-remote-dashboard-welcome' );?></h1>
		<form action='options.php' method='post'>
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		</form>
		</div>
	<?php

}

function rrdw_settings_section_callback( $arg ) {
	_e( 'Enter the url of your Remote Dashboard Welcome Control page below, the content of this page will be shown instead of the default WordPress Welcome on the dashboard.' , 'rbs-remote-dashboard-welcome' );
}


function addMetaReferrer() {
?>
	<meta name="referrer" content="origin">
<?php
}

add_action('admin_head','addMetaReferrer',1,1);

function my_plugin_deprecated_notice() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php _e( 'The Remote Dashboard Welcome plugin is deprecated and no longer maintained. Please consider finding an alternative.', 'rbs-remote-dashboard-welcome' ); ?></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'my_plugin_deprecated_notice' );


?>
