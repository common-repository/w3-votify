<?php
// https://codex.wordpress.org/Adding_Administration_Menus

add_action( 'admin_menu', 'w3vxff_add_admin_menu' );

function w3vxff_settings_tabs(){
	// I'm not sure if it makes sense to include pre_html and post_html since the first and last inputs in a tab array could be used to define the same valies.
	
	// $tabs[$formName][$fieldsetName]

	
}


function w3vxff_settings_fieldsets(){

}

function w3vxff_settings(){
	global $wpdb, $wp_roles;
	
	# FORM NAME	
	
	$form_name = "w3vxff-settings";
	

	# SETUP
	
	$setup["type"] = "settings";
	w3vxff_register_form($setup, "setup", $form_name);
	


	# TABS
	
	$tabs["general_tab"] = array(
		"title" => "General",						
	);

	w3vxff_register_form($tabs, "tabs", $form_name);	



	# FIELDSETS
	
	$fieldsets["general_fieldset"] = array(
		"title" => "Auto Insert Vote Buttons",
		"repeater" => false,
	);
	
		
	w3vxff_register_form($fieldsets, "fieldsets", $form_name);	
	//    $w3vxff_FORMS[$form_name][$type]["general"]["repeater"] = $args;


	# Post Types
	
	foreach(get_post_types() as $cpt){
		$postTypes[$cpt] = $cpt; 
	}

		
	### REMEMBER: Avoid providing default values for multiselect fields since the user may not want to provide a value ("null"), but a default value will override an empty input field.
	$fields = array(
		"general_tab" => array(
			"general_fieldset" => array(		
				array(
					"name" => "before_post",
					"label" => "Before Post",
					"type" => "multiselect",
					"options" => $postTypes,
					"description" => "Select post types to automatically insert vote buttons."
				),
				array(
					"name" => "after_post",
					"label" => "After Post",
					"type" => "multiselect",
					"options" => $postTypes,
					"description" => "Select post types to automatically insert vote buttons."
				),
				array(
					"name" => "before_comment",
					"label" => "Before Comment",
					"type" => "select",
					"options" => array(
						0 => "Disable",
						1 => "Enable",						
					),
				),
				array(
					"name" => "after_comment",
					"label" => "After Comment",
					"type" => "select",
					"options" => array(
						0 => "Disable",
						1 => "Enable",						
					),
				),				
			),
		),
	);

    w3vxff_register_form($fields, "fields", $form_name);
}

// remember admin_init is for the wp-admin and init is for the front-end
add_action("admin_init", "w3vxff_settings");
add_action("init", "w3vxff_settings");

function w3vxff_options_page() {

	global $w3vxff_FORMS;

	?>

	<?php
	
	echo w3vxff_display_form($w3vxff_FORMS["w3vxff-settings"]["fields"], "w3vxff-settings");
}

function w3vxff_get_plugin_directory(){
	$directory = array();
	
	$directory['path'] = trailingslashit( plugin_dir_path( __FILE__ ) );
	$directory['url'] = plugin_dir_url( __FILE__ );
	return $directory;
}


/**
 * The enqueue function
 * Registers and enqueue the CSS
 */
function w3vxff_admin_css_custom_page() {

	// https://www.intechgrity.com/how-to-add-your-own-stylesheet-to-your-wordpress-plugin-settings-page-or-all-admin-page/#

	$pluginDirectory = w3vxff_get_plugin_directory();

	# REGISTER STYLES
    wp_register_style( 'sumoselect',  $pluginDirectory['url'].'settings/assets/css/sumoselect.css', array(), 1 );	
    wp_register_style( 'bootstrap',  $pluginDirectory['url'].'settings/assets/css/bootstrap/bootstrap.css', array(), 1 );
    wp_register_style( 'w3vxff-admin',  $pluginDirectory['url'].'assets/css/admin.css', array(), 1 );
    wp_register_style( 'w3vxff-app',  $pluginDirectory['url'].'settings/assets/css/app.css', array(), 1 );


	# REGISTER SCRIPTS
	wp_register_script( "sumoselect", $pluginDirectory["url"].'settings/assets/js/jquery.sumoselect.js', array("jquery"), null, true );
	wp_register_script( "sf-repeater", $pluginDirectory["url"].'settings/assets/js/repeater.js', array("jquery"), null, true );
	wp_register_script( "bootstrap", $pluginDirectory["url"].'settings/assets/js/bootstrap/bootstrap.js', array("jquery"), null, true );
	wp_register_script( "bootbox", $pluginDirectory["url"].'settings/assets/js/bootstrap/bootbox.js', array("bootstrap"), null, true );
 	wp_register_script( "w3vxff-admin", $pluginDirectory["url"].'assets/js/admin.js', array("jquery"), null, true ); // this is code that's meant to augment the W3 Search Fields settings page.
  	wp_register_script( "w3vxff-app", $pluginDirectory["url"].'settings/assets/js/app.js', array("jquery"), null, true );

 
    # ENQUEUE
    // styles
	wp_enqueue_style('sumoselect');   
    wp_enqueue_style('bootstrap');
    wp_enqueue_style('w3vxff-admin');
    wp_enqueue_style('w3vxff-app');
    
    // scripts
    // REMEMBER: the order scripts load matters
	wp_enqueue_script('jquery');
	wp_enqueue_script('sumoselect');
	wp_enqueue_script('sf-repeater');
	wp_enqueue_script('bootstrap');
	wp_enqueue_script('bootbox');	
	wp_enqueue_script('w3vxff-admin');
	wp_enqueue_script('w3vxff-app');

   $script_params = array(
	   /* examples */
	   'ajaxurl' => admin_url('admin-ajax.php'),
   );

   wp_localize_script( 'w3vxff-admin', 'MyAjax', $script_params );
	
}

/**
 * The function to hook the enqueue function
 * And Add the Menu page
 * We can seperate the enqueue but this is the best practice
 * Use a little of your head and find out why?
 */
function w3vxff_add_admin_menu() {
    /** Add options page and get the reference through a variable */
	$w3vxff = add_options_page( 'W3 Votify', 'W3 Votify', 'manage_options', 'w3vxff', 'w3vxff_options_page' );
 
    /**
     * Now use the reference to conditionally attach the script
     * We will add an action to admin_print_style with conditional tag
     * Also we will execute the w3vxff_admin_css_custom_page function which enqueues the CSS
     */
    add_action('admin_print_styles-' . $w3vxff, 'w3vxff_admin_css_custom_page');
}


function w3vxff_get_settings_value($tab, $key, $default = null){
	global $w3vxff_FORMS;
	
	$dbValues = get_option("w3vxff_settings");// mega value contaning w3vxff wp-admin settings
	
	if(isset($dbValues)){
		$dbValues = json_decode($dbValues, true);
		
		// REMEMBER: repeaters are an array of values stored like regular fields. Repeater values will need to be further processes (as needed) once return via this function.
		return isset($dbValues["w3vxff-settings"][$tab][$key]) ? $dbValues["w3vxff-settings"][$tab][$key] : null;
	}
	
	if(empty($formValues)){ // if saved values are not found, return form default
		foreach($w3vxff_FORMS["w3vxff-settings"]["fields"]["settings_tab"] as $k => $v){
			if($key == $k){
				return $v;
			}
		}		
	}
	
	// if no other returns found, return $default (user/developer-defined fallback value)
	return $default;	
}


?>