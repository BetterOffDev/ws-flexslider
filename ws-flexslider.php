<?php
/*
Plugin Name: WS Flexslider
Description: Custom plugin to use the jQuery Flexslider to create a featured content gallery on the main page of the site
Version: 1.0
Author: Will Spencer  follow me on Twitter @wspencer428
License: GPL2
*/


/*************************************
 Include the Style Sheet 
*************************************/

add_action('wp_enqueue_scripts', 'ws_flexslider_style');

	function ws_flexslider_style() {

		echo "<link type='text/css' rel='stylesheet' href='" . plugins_url('/flexslider.css', __FILE__) . "' />";

}

/************************************
  Flex Slider Script Controls 
************************************/
define('FS_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );  
define('FS_NAME', "WSFlexSlider");  
define ("FS_VERSION", "1.0");

wp_enqueue_script('ws_flexslider', FS_PATH.'jquery.flexslider.js', array('jquery'));  
wp_enqueue_style('ws_flexslider_css', FS_PATH.'flexslider.css');

function ws_flexslider_script(){  
  
	print '<script type="text/javascript" charset="utf-8"> 
	  jQuery(window).load(function() { 
	    jQuery(\'.flexslider\').flexslider({
			animation: "slide",
			controlsContainer: ".flex-nav-container",
			pauseOnHover: true,
		}); 
	  }); 
	</script>';
  
}  
  
add_action('wp_head', 'ws_flexslider_script');

function ws_get_slider(){  
  
    $slider= '<div class="flexslider"> 
          <ul class="slides">';  
		global $do_not_duplicate;
      	$do_not_duplicate = array();
        $args= "cat=4&posts_per_page=5";  
        $fs_query = new WP_Query($args);  
      
        if ($fs_query->have_posts()) : while ($fs_query->have_posts()) : $fs_query->the_post();  
			$do_not_duplicate[] = get_the_ID();
            $img= get_the_post_thumbnail( $post->ID, 'slider', array(
						'alt'	=> trim(strip_tags( $attachment->post_title )),
						'class'	=> "center",
						'title'	=> trim(strip_tags( $attachment->post_title )),
					) );
 			$id = get_the_ID();
			$link= get_permalink( $id );
			$title= get_the_title( $id );
			$excerpt= get_post_meta( get_the_ID(), 'wsdev_fs_excerpt', true);
			
      		
           	$slider.='<li class="center"><a href="' .$link. '">' .$img. '</a><p class="flex-caption">' .$title. '<br /><span class="flex-excerpt">' .$excerpt. '</span></p></li>';
			
			
 		endwhile; endif; wp_reset_query();  
      
        $slider.= '</ul> 
        </div>';  
      
        return $slider;

}

/* Add template tag */  
  
function home_slider(){  
  
    print ws_get_slider();  
}

/* Add the custom excerpt box for use in the slider */


$prefix = 'wsdev_';

/*********************************
	Featured Excerpt Meta Box
*********************************/


// Add meta box
function wsdev_add_fs_excerpt_box() {
    global $post;
 
    // Define the noncename
    wp_nonce_field( plugin_basename(__FILE__), 'wsdev_fs_excerpt_noncename' );
 
    // Get the data if its already been entered
    $wsdev_excerpt = get_post_meta($post->ID, 'wsdev_fs_excerpt', true );
    
    // HTML for meta box form
    ?>
    
    <textarea name="_notes" rows="10" cols="100"/><?php echo $wsdev_excerpt; ?></textarea>
    <?php
}

add_action('save_post', 'wsdev_save_data', 1, 2);

// Save data from meta box
function wsdev_save_data($post_ID, $post) {

    // verify the nonce
    if ( !wp_verify_nonce( $_POST['wsdev_excerpt_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
    }
 
    // check user status
    if ( !empty($_POST) && check_admin_referer( plugin_basename(__FILE__), 'introbox_noncename') )
 
    $wsdev_excerpt['wsdev_fs_excerpt'] = esc_textarea( $_POST['wsdev_fs_excerpt'] );
 
    // Add or update values of $intro_meta to the db
 
    foreach ($wsdev_excerpt as $key => $value) { 
        if( $post->post_type == 'revision' ) return; 
      
        if(get_post_meta($post->ID, $key, FALSE)) { 
            update_post_meta($post->ID, $key, $value);
        } else { 
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); 
    }
}

/* Create Template Tags */

function fs_excerpt() {
	
	echo get_post_meta( get_the_ID(), 'wsdev_fs_excerpt', true);
}