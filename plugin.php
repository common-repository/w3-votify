<?php
/**
Plugin Name: W3 Votify
Version: 1.0.8
Author: W3Extensions
Plugin URI: https://wordpress.org/plugins/w3-votify/
Author URI: http://w3extensions.com
Description: Add upvote/downvote buttons to your WordPress posts and comments.
Contributors: bookbinder
Tags: social bookmarking, ajax, voting
Requires at least: 4.7
Tested up to: 4.9.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

require_once("settings/autoloader.php");
require_once("w3ff.php");
require_once("inc/vote.php");

function w3vx_activation(){
	if(function_exists("w3vx_create_user_votes_table")){
		w3vx_create_user_votes_table();
	}
}

register_activation_hook( __FILE__, 'w3vx_activation' );



function w3vx_get_plugin_directory(){
	$directory = array();
	
	$directory['path'] = trailingslashit( plugin_dir_path( __FILE__ ) );
	$directory['url'] = plugin_dir_url( __FILE__ );
	return $directory;
}

function w3vx_scripts() {
	$pluginDirectory = w3vx_get_plugin_directory();

	$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
	$plugin_version = $plugin_data['Version'];	

	#CSS
	wp_register_style( 'w3vx',  $pluginDirectory['url'].'assets/css/app.css', array(), $plugin_version );
	wp_enqueue_style('w3vx');
	
	#JS
	wp_register_script( "w3vx", $pluginDirectory['url'].'assets/js/app.js', array("jquery"), $plugin_version, true );
	
	
	wp_enqueue_script('jquery');
	
	wp_enqueue_script('w3vx');
	wp_localize_script( 'w3vx', 'w3vx_ajax', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
		"loggedin" => intval(is_user_logged_in())
	) );	
}

add_action('wp_enqueue_scripts', 'w3vx_scripts', 100);


function w3vx_button_style() {
$icon = "triangle";
	$base = w3vx_get_plugin_directory()["url"]."assets/img/";
	$hover = $base.$icon."-red.svg";
	$active = $base.$icon."-red.svg";
	$grey = $base.$icon."-grey.svg";
	
	echo "
		<style>
			.post-vote .vote-button.vote-up:hover,
			.post-vote .vote-button.vote-down:hover, 
			.post-vote .vote-button.vote-up.active,
			.post-vote .vote-button.vote-down.active,
			.post-vote .vote-button.vote-up,
			.post-vote .vote-button.vote-down {
				width:35px;
				height:25px;
			}
			
			.comment-vote .vote-button.vote-up:hover,
			.comment-vote .vote-button.vote-down:hover, 
			.comment-vote .vote-button.vote-up.active,
			.comment-vote .vote-button.vote-down.active,
			.comment-vote .vote-button.vote-up,
			.comment-vote .vote-button.vote-down {
				width:20px;
				height:18px;
			}			
			
			.vote-button.vote-up:hover,
			.vote-button.vote-down:hover {
				background: url(".$hover.");
				background-size: 90% 90%;
				background-repeat: no-repeat;
				background-position: center center;	
			}
			
			.vote-button.vote-up.active,
			.vote-button.vote-down.active {
				background: url(".$active.");
				background-size: 90% 90%;
				background-repeat: no-repeat;
				background-position: center center;	
			}
			
			.vote-button.vote-up,
			.vote-button.vote-down {
				background: url(".$grey.");
				background-size: 90% 90%;
				background-repeat: no-repeat;
				background-position: center center;	
			}
			
			.vote-button.vote-down {
				transform: rotate(180deg);
			}
		</style>
	";
      
} 
add_action('wp_head', 'w3vx_button_style');


function w3vx_filter_content($content){
	global $post;

	$before = (array) w3vxff_get_settings_value("general_tab", "before_content");
	$after = (array) w3vxff_get_settings_value("general_tab", "after_content", "post");
	
	
	if(in_array($post->post_type, $before)){
		$content = w3vx_vote_buttons_wrapper($post->ID, "post", "triangle", "horizontal") . $content;
	} 
	
	//someone may want vote buttons to appear before and after content, so we'll use two separate if statements, instead of if/elseif
	if(in_array($post->post_type, $after)){
		$content .= w3vx_vote_buttons_wrapper($post->ID, "post", "triangle", "horizontal");
	}
	
	return $content; 
}
add_filter("the_content", "w3vx_filter_content", 20, 3);



function w3vx_filter_comment( $comment ) {
	$comment_ID = get_comment_ID();

	$before = (boolean) w3vxff_get_settings_value("general_tab", "before_comment");
	$after = (boolean) w3vxff_get_settings_value("general_tab", "after_comment");
	
	
	if($before){
		$comment = w3vx_vote_buttons_wrapper($comment_ID, "comment", "triangle", "horizontal") . $comment;
	} 
	
	//someone may want vote buttons to appear before and after comment content, so we'll use two separate if statements, instead of if/elseif
	if($after){
		$comment .= w3vx_vote_buttons_wrapper($comment_ID, "comment", "triangle", "horizontal");
	}
	
	return $comment;
}

add_filter( "comment_text", "w3vx_filter_comment", 30, 3 ); // the third paranter, "30" is important, since we need this filter to be executed after WP has finoshed running filters that could add unwanted HTML tags like <br> (for every line break in the html string appended to the $ocmment).
?>