<?php
/*
Plugin Name: Ajax Load More for Relevanssi
Plugin URI: http://connekthq.com/plugins/ajax-load-more/extensions/relevanssi/
Description: An Ajax Load More extension that adds compatibility with Relevanssi
Text Domain: ajax-load-more-for-relevanssi
Author: Darren Cooney
Twitter: @KaptonKaos
Author URI: https://connekthq.com
Version: 1.0.1
License: GPL
Copyright: Darren Cooney & Connekt Media

// @codingStandardsIgnoreStart
*/

if(!class_exists('ALM_Relevanssi')) :   
   
	/**
	 * ALM Relevanssi Class.
	 * 
	 * @author ConnektMedia <darren@connekthq.com>
	 */
   class ALM_Relevanssi{	   
      
		/**
		 * Construct Relevanssi class.
		 */
   	function __construct(){	
         add_filter('alm_relevanssi', array(&$this, 'alm_relevanssi_get_posts'), 10, 1);	
      }           
      
      /**
   	 * Get relevanssi search results and return post ids in post__in wp_query param.
   	 *
   	 * @return $args
   	 * @since  1.0
   	 */
      function alm_relevanssi_get_posts($args){
 
      	if(function_exists('relevanssi_do_query')){         	
         	
         	$old_posts_per_page = $args['posts_per_page'];
         	$old_offset = $args['offset'];
         	$args['orderby'] = 'relevance';
         	$args['posts_per_page'] = -1; // We need to get all search results for this to work
         	$args['offset'] = 0; // We don't want an offset (ALM handles this)         	
         	
         	$wp_query = new WP_Query( $args );   
         	
         	// Core Relevanssi Filter.
         	$wp_query = apply_filters( 'relevanssi_modify_wp_query', $wp_query );  
         	
         	// Run query. 	
         	relevanssi_do_query( $wp_query );  
         	
				// Empty posts array.
         	$posts = [];
         	
         	// Loop all results and pull post IDs
         	// 'fields' => 'ids' is not working in some environments.
         	if($wp_query->posts){
	         	foreach($wp_query->posts as $result){
		         	$posts[] = $result->ID;
	         	}
         	} 
         	
      		if ( ! empty( $posts ) ) {         		      			      			
      			$args['post__in']       = $posts; // $relevanssi_query->posts array
      			$args['orderby']        = 'post__in'; // Override orderby to relevance
					$args['search']         = $args['s']; // Set custom `search` arg to pass back to ALM. 
      			$args['s']              = ''; // Reset 's' term value
      			$args['posts_per_page'] = $old_posts_per_page; // Reset 'posts_per_page' before sending data back
      			$args['offset']         = $old_offset; // Reset 'offset' before sending data back

      		}  
      		    			
      		return $args;	
      	}        	    	
      }
   }  
   
   /**
    * ALM_Relevanssi
    * The main function responsible for returning the one true ALM_Relevanssi Instance.
    *
	 * @since 1.0
	 * @author ConnektMedia <darren@connekthq.com>
    */
   function ALM_Relevanssi(){
      global $ALM_Relevanssi;
      
      if( !isset($ALM_Relevanssi) ){
         $ALM_Relevanssi = new ALM_Relevanssi();
      }
      
      return $ALM_Relevanssi;
   }      
   ALM_Relevanssi();
   
endif;