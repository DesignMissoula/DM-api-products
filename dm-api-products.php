<?php
/*
Plugin Name: DM API Products
Plugin URI: http://designmissoula.com/
Description: This is not just a plugin, it symbolizes the hope of every page.
Author: Bradford Knowlton
Version: 1.0.1
Author URI: http://bradknowlton.com/
*/

// Initialize Settings
require_once(sprintf("%s/inc/settings.php", dirname(__FILE__)));


if( is_admin() ){
    $my_settings_page = new MySettingsPage();
}




// define the woocommerce_short_description callback 
function filter_woocommerce_short_description( $post_post_excerpt ) { 
    // make filter magic happen here... 
    
    global $json_data;
    
    // global $product;
    
    // $product->product_url = "http://www.google.com/";
    
    $part_number = get_the_title();
    
    // Get any existing copy of our transient data
			if ( false === ( $json_data = get_transient( 'dm_api_products_'.md5( $part_number ) ) ) ) {
			    // It wasn't there, so regenerate the data and save the transient
			    
			    $url = "http://productfinder.pulseeng.com/api_sap/products";
			    
			    $options = get_option( 'my_option_name' );
			    
			    $body = array( 	'app_id' => $options['app_id'], 
			    				'secret_key' => $options['secret_key'], 
			    				'part_number' => $part_number 
			    			);
			    
			    $args['body'] = json_encode( $body );
			    
			    $args['headers'] = array( 'Content-Type' => 'application/json' );
			    
			    // var_dump( $args );
			    
				$response = wp_remote_post( $url, $args );

				// var_dump($response);

				if( is_array($response) ) {
				  $header = $response['headers']; // array of http header lines
				  $body = $response['body']; // use the content
				}

				$json_data = json_decode($body);
			    set_transient( 'dm_api_products_'.md5( $part_number ), $json_data, 12 * HOUR_IN_SECONDS );
			}else {
			 echo "<!-- !cache hit! -->";
			}
   
    if( 0 < count($json_data->data )){
    
    			$post_post_excerpt = "";

				foreach($json_data->data as $key => $product){
				
					// var_dump($product);

					$post_post_excerpt .= '<p>Part Number: '.$part_number.'</p>';
					
					$post_post_excerpt .= '<hr/>';
					
					if( isset($product->datasheets) && 0 < count( $product->datasheets ) ){
						
						// $post_post_excerpt .= '<h2>Datasheets</h2>';
						foreach($product->datasheets as $datasheet){
							$post_post_excerpt .= '<p class="cart"><a href="http://productfinder.pulseeng.com/files/datasheets/'.$datasheet.'" target="_BLANK" class="single_add_to_cart_button button alt">Download Datasheet</a></p>';	
						}	
						
						$post_post_excerpt .= '<hr/>';
					}
					
		}
		
		}
    
    
    return $post_post_excerpt; 
}; 
         
// add the filter 
add_filter( 'woocommerce_short_description', 'filter_woocommerce_short_description', 10, 1 ); 
add_filter( 'get_the_excerpt', 'filter_woocommerce_short_description', 10, 1 ); 

// remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );    


// define the woocommerce_short_description callback 
function filter_woocommerce_content( $post_post_excerpt ) { 
    // make filter magic happen here... 
    
    global $json_data;
    
    $part_number = get_the_title();
    
    if( get_post_type( get_the_ID() ) != 'product' ){
	    return $post_post_excerpt;
    }
    
    // Get any existing copy of our transient data
			if ( false === ( $json_data = get_transient( 'dm_api_products_'.md5( $part_number ) ) ) ) {
			    // It wasn't there, so regenerate the data and save the transient
			    
			    $url = "http://productfinder.pulseeng.com/api_sap/products";
			    
			    $options = get_option( 'my_option_name' );
			    
			    $body = array( 	'app_id' => $options['app_id'], 
			    				'secret_key' => $options['secret_key'], 
			    				'part_number' => $part_number 
			    			);
			    
			    $args['body'] = json_encode( $body );
			    
			    $args['headers'] = array( 'Content-Type' => 'application/json' );
			    
			    // var_dump( $args );
			    
				$response = wp_remote_post( $url, $args );

				// var_dump($response);

				if( is_array($response) ) {
				  $header = $response['headers']; // array of http header lines
				  $body = $response['body']; // use the content
				}

				$json_data = json_decode($body);
			    set_transient( 'dm_api_products_'.md5( $part_number ), $json_data, 12 * HOUR_IN_SECONDS );
			}else {
			 echo "<!-- !cache hit! -->";
			}
   
    if( 0 < count($json_data->data )){
    
    			$post_post_excerpt = "";

				foreach($json_data->data as $key => $product){
				
					// var_dump($product);

					// $post_post_excerpt .= '<img src="http://productfinder.pulseeng.com/files/images/products/'.$key.'.jpg" />';
					
					
					if( isset($product->fields) && 0 < count( $product->fields ) ){
						
						$post_post_excerpt .= '<dl>';
						foreach($product->fields as $key => $field){
							$post_post_excerpt .= '<dt>'.$key.'</dt>';	
							$post_post_excerpt .= '<dd>'.$field.'</dd>';	
						}	
						$post_post_excerpt .= '</dl>';
						
					}
					
				}
		
		}
    
    
    return $post_post_excerpt; 
}; 
         
// add the filter 
add_filter( 'the_content', 'filter_woocommerce_content', 10, 1 );     
    

function custom_rewrite_rule() {
    add_rewrite_rule('^nutrition/([^/]*)/([^/]*)/?','index.php?page_id=12&food=$matches[1]&variety=$matches[2]','top');
}
// add_action('init', 'custom_rewrite_rule', 10, 0);


function prefix_movie_rewrite_rule() {
    // add_rewrite_rule( 'movie/([^/]+)/photos', 'index.php?movie=$matches[1]&photos=yes', 'top' );
    // add_rewrite_rule( 'movie/([^/]+)/videos', 'index.php?movie=$matches[1]&videos=yes', 'top' );
    
    add_rewrite_rule( 'product/([^/]*)/([^/]*)', 'index.php?product=$matches[1]%2F$matches[2]', 'top' );
    add_rewrite_rule( 'product/([^/]*)/([^/]*)/', 'index.php?product=$matches[1]%2F$matches[2]', 'top' );
    
    add_rewrite_rule( 'product/([^/]*)', 'index.php?product=$matches[1]', 'top' );
    add_rewrite_rule( 'product/([^/]*)/', 'index.php?product=$matches[1]', 'top' );
    
}
 
// add_action( 'init', 'prefix_movie_rewrite_rule' );


function prefix_register_query_var( $vars ) {
    // $vars[] = 'photos';
    // $vars[] = 'videos';
    $vars[] = 'product';
 
    return $vars;
}
 
// add_filter( 'query_vars', 'prefix_register_query_var' );



function prefix_url_rewrite_templates() {
 
    if ( get_query_var( 'product' ) ) {
        add_filter( 'template_include', function() {
            return get_template_directory() . '/page.php';
        });
    }
}
 
// add_action( 'template_redirect', 'prefix_url_rewrite_templates' );

// add_action( 'template_redirect', 'xyz_create_fake_query' );
function xyz_create_fake_query() {

	global $wp, $wp_query, $json_data;
	
	// var_dump(get_query_var( 'product' ));

	if ( get_query_var( 'product' ) ) {
			
			// Get any existing copy of our transient data
			if ( false === ( $json_data = get_transient( 'dm_api_products_'.md5( get_query_var( 'product' ) ) ) ) ) {
			    // It wasn't there, so regenerate the data and save the transient
			    
			    $url = "http://productfinder.pulseeng.com/api_sap/products";
			    
			    $options = get_option( 'my_option_name' );
			    
			    $body = array( 	'app_id' => $options['app_id'], 
			    				'secret_key' => $options['secret_key'], 
			    				'part_number' => get_query_var( 'product' ) 
			    			);
			    
			    $args['body'] = json_encode( $body );
			    
			    $args['headers'] = array( 'Content-Type' => 'application/json' );
			    
			    // var_dump( $args );
			    
				$response = wp_remote_post( $url, $args );

				// var_dump($response);

				if( is_array($response) ) {
				  $header = $response['headers']; // array of http header lines
				  $body = $response['body']; // use the content
				}

				$json_data = json_decode($body);
			    set_transient( 'dm_api_products_'.md5( get_query_var( 'product' ) ), $json_data, 12 * HOUR_IN_SECONDS );
			}else {
			 echo "<!-- !cache hit! -->";
			}

			// echo "<ul>";
			
			// var_dump($json_data);

			$column_label = array( '1'=>'', '2'=>'-second', '3'=>'-third', '4'=>'-fourth', '5'=>'-fifth', '6'=>'-sixth', );

			if( 0 < count($json_data->data )){

				foreach($json_data->data as $key => $product){
				
					var_dump($product);

					// Create our fake post
					$post_id = -99;
					
					$content .= '<img src="http://productfinder.pulseeng.com/files/images/products/'.$key.'.jpg" />';
					
					
					if( isset($product->fields) && 0 < count( $product->fields ) ){
						
						$content .= '<h2>Specifications</h2><dl>';
						foreach($product->fields as $key => $field){
							$content .= '<dt>'.$key.'</dt>';	
							$content .= '<dd>'.$field.'</dd>';	
						}	
						$content .= '</dl>';
						
					}
					
					if( isset($product->datasheets) && 0 < count( $product->datasheets ) ){
						
						$content .= '<h2>Datasheets</h2>';
						foreach($product->datasheets as $datasheet){
							$content .= '<p><a href="http://productfinder.pulseeng.com/files/datasheets/'.$datasheet.'" target="_BLANK" >Download PDF</a></p>';	
						}	
						
					}
					
					
					// http://productfinder.pulseeng.com/files/datasheets/ASB11 700 800 900 1850 MHZ ANTENNAS.pdf
					
					
					
					$post_id = -99; // negative ID, to avoid clash with a valid post
					$post = new stdClass();
					$post->ID = $post_id;
					$post->post_author = 1;
					$post->post_date = current_time( 'mysql' );
					$post->post_date_gmt = current_time( 'mysql', 1 );
					$post->post_title = 'Product: '.$key;
					$post->post_content = $content;
					$post->post_status = 'publish';
					$post->comment_status = 'closed';
					$post->ping_status = 'closed';
					$post->post_name = 'fake-page-' . rand( 1, 99999 ); // append random number to avoid clash
					$post->post_type = 'page';
					$post->filter = 'raw'; // important

					// Convert to WP_Post object
					$wp_post = new WP_Post( $post );
				
					// Add the fake post to the cache
					wp_cache_add( $post_id, $wp_post, 'posts' );
					
					// Update the main query
					$wp_query->post = $wp_post;
					$wp_query->posts = array( $wp_post );
					$wp_query->queried_object = $wp_post;
					$wp_query->queried_object_id = $post_id;
					$wp_query->found_posts = 1;
					$wp_query->post_count = 1;
					$wp_query->max_num_pages = 1; 
					$wp_query->is_page = true;
					$wp_query->is_singular = true; 
					$wp_query->is_single = true; 
					$wp_query->is_attachment = false;
					$wp_query->is_archive = false; 
					$wp_query->is_category = false;
					$wp_query->is_tag = false; 
					$wp_query->is_tax = false;
					$wp_query->is_author = false;
					$wp_query->is_date = false;
					$wp_query->is_year = false;
					$wp_query->is_month = false;
					$wp_query->is_day = false;
					$wp_query->is_time = false;
					$wp_query->is_search = false;
					$wp_query->is_feed = false;
					$wp_query->is_comment_feed = false;
					$wp_query->is_trackback = false;
					$wp_query->is_home = false;
					$wp_query->is_embed = false;
					$wp_query->is_404 = false; 
					$wp_query->is_paged = false;
					$wp_query->is_admin = false; 
					$wp_query->is_preview = false; 
					$wp_query->is_robots = false; 
					$wp_query->is_posts_page = false;
					$wp_query->is_post_type_archive = false;
								

					
				}


			}else{

				// Create our fake post
				$post_id = -99;
				
				$post_id = -99; // negative ID, to avoid clash with a valid post
				$post = new stdClass();
				$post->ID = $post_id;
				$post->post_author = 1;
				$post->post_date = current_time( 'mysql' );
				$post->post_date_gmt = current_time( 'mysql', 1 );
				$post->post_title = 'Product: Not Found';
				$post->post_content = 'Sorry Product Not Found';
				$post->post_status = 'publish';
				$post->comment_status = 'closed';
				$post->ping_status = 'closed';
				$post->post_name = 'fake-page-' . rand( 1, 99999 ); // append random number to avoid clash
				$post->post_type = 'page';
				$post->filter = 'raw'; // important

				// Convert to WP_Post object
				$wp_post = new WP_Post( $post );
			
				// Add the fake post to the cache
				wp_cache_add( $post_id, $wp_post, 'posts' );
				
				// Update the main query
				$wp_query->post = $wp_post;
				$wp_query->posts = array( $wp_post );
				$wp_query->queried_object = $wp_post;
				$wp_query->queried_object_id = $post_id;
				$wp_query->found_posts = 1;
				$wp_query->post_count = 1;
				$wp_query->max_num_pages = 1; 
				$wp_query->is_page = false;
				$wp_query->is_singular = false; 
				$wp_query->is_single = false; 
				$wp_query->is_attachment = false;
				$wp_query->is_archive = false; 
				$wp_query->is_category = false;
				$wp_query->is_tag = false; 
				$wp_query->is_tax = false;
				$wp_query->is_author = false;
				$wp_query->is_date = false;
				$wp_query->is_year = false;
				$wp_query->is_month = false;
				$wp_query->is_day = false;
				$wp_query->is_time = false;
				$wp_query->is_search = false;
				$wp_query->is_feed = false;
				$wp_query->is_comment_feed = false;
				$wp_query->is_trackback = false;
				$wp_query->is_home = false;
				$wp_query->is_embed = false;
				$wp_query->is_404 = true; 
				$wp_query->is_paged = false;
				$wp_query->is_admin = false; 
				$wp_query->is_preview = false; 
				$wp_query->is_robots = false; 
				$wp_query->is_posts_page = false;
				$wp_query->is_post_type_archive = false;
							
			}

		

	
	
	
	$GLOBALS['wp_query'] = $wp_query;
	$wp->register_globals();
	
	}
}