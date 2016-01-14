<?php
/**
 * Убираем мусор из шапки
 *
 * @link https://wordpress.org/
 *
 * @package Starship
 */

    remove_action('wp_head', 'wp_generator');
 
    remove_action('wp_head', 'wlwmanifest_link');
 
    remove_action('wp_head', 'rsd_link');
 
    remove_action( 'wp_head', 'wp_shortlink_wp_head');
 
    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head');
 
    remove_action( 'wp_head', 'feed_links_extra', 3);
 
    remove_action( 'wp_head', 'feed_links', 2 );
 
    remove_action('wp_head','rel_canonical');
    
    // REMOVE EMOJI ICONS
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');

    /*
     *Kama1467@
     *Нужно удалить все фильтры REST API и отключить сам API. Для этого поместите следующий код в файл functions.php:
    */
   
	// Отключаем сам REST API
	add_filter('rest_enabled', '__return_false');

	// Отключаем фильтры REST API
	remove_action( 'xmlrpc_rsd_apis',            'rest_output_rsd' );
	remove_action( 'wp_head',                    'rest_output_link_wp_head', 10, 0 );
	remove_action( 'template_redirect',          'rest_output_link_header', 11, 0 );
	remove_action( 'auth_cookie_malformed',      'rest_cookie_collect_status' );
	remove_action( 'auth_cookie_expired',        'rest_cookie_collect_status' );
	remove_action( 'auth_cookie_bad_username',   'rest_cookie_collect_status' );
	remove_action( 'auth_cookie_bad_hash',       'rest_cookie_collect_status' );
	remove_action( 'auth_cookie_valid',          'rest_cookie_collect_status' );
	remove_filter( 'rest_authentication_errors', 'rest_cookie_check_errors', 100 );

	// Отключаем события REST API
	remove_action( 'init',          'rest_api_init' );
	remove_action( 'rest_api_init', 'rest_api_default_filters', 10, 1 );
	remove_action( 'parse_request', 'rest_api_loaded' );

	// Отключаем Embeds связанные с REST API
	remove_action( 'rest_api_init',          'wp_oembed_register_route'              );
	remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4 );

        //Remove json+oembed link
        /*
	function remove_json_api () {

	    // Remove the REST API lines from the HTML Header
	    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	    remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

	    // Remove the REST API endpoint.
	    remove_action( 'rest_api_init', 'wp_oembed_register_route' );

	    // Turn off oEmbed auto discovery.
	    add_filter( 'embed_oembed_discover', '__return_false' );

	    // Don't filter oEmbed results.
	    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

	    // Remove oEmbed discovery links.
	    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	    // Remove oEmbed-specific JavaScript from the front-end and back-end.
	    remove_action( 'wp_head', 'wp_oembed_add_host_js' );

	   // Remove all embeds rewrite rules.
	   add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );

	}
	add_action( 'after_setup_theme', 'remove_json_api' );
       */
	


	// Remove the annoying:
	// <style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style> added in the header
	function remove_recent_comment_style() {
		global $wp_widget_factory;
		remove_action( 
		    'wp_head', 
		    array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) 
		);
	}
	add_action( 'widgets_init', 'remove_recent_comment_style' );
    

