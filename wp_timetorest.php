<?php
/**
 * Plugin Name:     Lightweb Api Workflow
 * Plugin URI:      https://github.com/3ele-Lightweb/wp-timetorest
 * Description:     RETURN ON example.com/wp-json/lightweb/v1/jobs text jobs for texter
 * Author:          Sebastian Weiss
 * Author URI:      www.3ele.de
 * Domain Path:     /languages
 * Version:         0.1.0
 *

 */
add_action( 'init', 'github_plugin_updater_test_init' );
if( ! class_exists( 'Smashing_Updater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . 'updater.php' );
}

$updater = new Smashing_Updater( __FILE__ );
$updater->set_username( '3ele-Lightweb' );
$updater->set_repository( 'wp-timetorest' );
/*
	$updater->authorize( 'abcdefghijk1234567890' ); // Your auth code goes here for private repos
*/
$updater->initialize();

function latest_posts($data) {
    $mod = $data['mod'];
    $count = $data['count'];
    $query =  new WP_Query( array(
        'post_status' => 'publish',
        'date_query' => array(
          array(
          'after' => $count.' '.$mod.' ago',
            ),
          ),
          'posts_per_page' => -1,
          'post_type' => 'any'
        ));

      if ($query->posts) {
        //var_dump($query->get_posts());
        $post_array = array(
          'job' => false,
          'posts' => $query->get_posts(),
        );
          $total = $query->found_posts;
           $response =  new WP_REST_Response($post_array ,200 ); 
          $response->header( 'X-WP-Total', $total ); // total = total number of post
        } else {
          $post_array = array(
            'job' => true,
          );

          $response = new WP_REST_Response($post_array,200);
          
        }
        return $response; 
  }

  add_action( 'rest_api_init', function () {
    register_rest_route( 'lw/v1', '/posts/(?P<mod>\w+)/(?P<count>\d+)/', array(
      'methods' => 'GET',
      'callback' => 'latest_posts',
      'args'   => array(
        'mod' => array(
            'description' => __( 'Accepts strings: ‘year’, ‘month’, ‘day’', 'lw' ),
            'type'        => 'string',
        ),
        'count' => array(
          'description' => __( 'after x ago.', 'lw' ),
          'type'        => 'integer',
      ),
    ),   
    ) );
  } );

