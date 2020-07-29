<?php
/**
 * Plugin Name:     Lightweb Api Workflow
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     RETURN ON example.com/wp-json/lightweb/v1/jobs text jobs for texter
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     lightweb-api-workflow
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Lightweb_Api_Workflow
 */
/*
 * @since 1.3
 * @version 1.0
 */
add_action( 'init', 'github_plugin_updater_test_init' );
function github_plugin_updater_test_init() {

	include_once 'updater.php';

	define( 'WP_GITHUB_FORCE_UPDATE', true );

	if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin

		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'github-updater',
			'api_url' => 'https://api.github.com/repos/3ele-Lightweb/wp-timetorest',
			'raw_url' => 'https://raw.github.com/3ele-Lightweb/wp-timetorest',
			'github_url' => 'https://github.com/3ele-Lightweb/wp-timetorest',
			'zip_url' => 'https://github.com/3ele-Lightweb/wp-timetorest//archive/master.zip',
			'sslverify' => true,
			'requires' => '3.0',
			'tested' => '3.3',
			'readme' => 'README.md',
			'access_token' => '',
		);

		new WP_GitHub_Updater( $config );

	}

}

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

  //add_action( 'init', create_function( '', 'global $WPGitHubUpdaterSetup; $WPGitHubUpdaterSetup = new WPGitHubUpdaterSetup();' ) );
