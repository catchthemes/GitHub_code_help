<?php
/** CPA Customization
 * Unknown function - find out what this is related to
 *
 * 
 */
function register_my_menus() {
	register_nav_menus( array(
		'primary' => __( 'Profile Menu' ),
		'sidebar-menu' => __( 'Sidebar Menu' )
	));
}
add_action( 'init', 'register_my_menus' );


function image_tag($html, $id, $alt, $title) {
	return preg_replace(array(
		'/'.str_replace('//','//',get_bloginfo('url')).'/i',
		'/s+width="d+"/i',
		'/s+height="d+"/i',
		'/alt=""/i'
		),
		array(
			'',
			'',
			'',
			'alt="' . $title . '"'
		),
		$html);
}
add_filter('get_image_tag', 'image_tag', 0, 4);


/** CPA Customization
 * Shows Featured Post Slider
 *
 * @uses catcheverest_header action to add it in the header
 */
function catcheverest_post_sliders() { 
	delete_transient( 'catcheverest_post_sliders' );
	
	global $post, $catcheverest_options_settings;
   	$options = $catcheverest_options_settings;

	
	if( ( !$catcheverest_post_sliders = get_transient( 'catcheverest_post_sliders' ) ) && !empty( $options[ 'featured_slider' ] ) ) {
		echo '<!-- refreshing cache -->';
		
		$catcheverest_post_sliders = '
		<div id="main-slider" class="container">
        	<section class="featured-slider">';
				$get_featured_posts = new WP_Query( array(
					'posts_per_page' => $options[ 'slider_qty' ],
					'post__in'		 => $options[ 'featured_slider' ],
					'orderby' 		 => 'post__in',
					'ignore_sticky_posts' => 1 // ignore sticky posts
				));
				$i=0; while ( $get_featured_posts->have_posts()) : $get_featured_posts->the_post(); $i++;
					$title_attribute = apply_filters( 'the_title', get_the_title( $post->ID ) );
					$excerpt = get_the_excerpt();
					if ( $i == 1 ) { $classes = "post hentry slides displayblock"; } else { $classes = "post hentry slides displaynone"; }
					$catcheverest_post_sliders .= '
					<article class="'.$classes.'">
						<figure class="slider-image">
							<a title="'.the_title('','',false).'" href="' . get_permalink() . '">
								'. get_the_post_thumbnail( $post->ID, 'slider', array( 'title' => esc_attr( $title_attribute ), 'alt' => esc_attr( $title_attribute ), 'class'	=> 'pngfix' ) ).'
							</a>	
						</figure>
						<div class="entry-container">
							<header class="entry-header">
								<h1 class="entry-title">
									<a title="'.the_title('','',false).'" href="' . get_permalink() . '">'.the_title( '<span>','</span>', false ).'</a>
								</h1>
							</header>';
							if( $excerpt !='') {
								$catcheverest_post_sliders .= '<div class="entry-content">'. $excerpt.'</div>';
							}
							$catcheverest_post_sliders .= '
						</div>
					</article><!-- .slides -->';				
				endwhile; wp_reset_query();
				$catcheverest_post_sliders .= '
			</section>
        	<div id="slider-nav">
        		<a class="slide-previous"><</a>
        		<a class="slide-next">></a>
        	</div>
        	<div id="controllers"></div>
  		</div><!-- #main-slider -->';
			
	set_transient( 'catcheverest_post_sliders', $catcheverest_post_sliders, 86940 );
	}
	echo $catcheverest_post_sliders;	
}


/** CPA Customization
 * Allow Mobile swip/gestures
 *
 * @uses catcheverest_header action to add it in the header
 */
function unhook_catcheverest_functions() {
	remove_action( 'wp_head', 'catcheverest_responsive', 5 );
}
add_action( 'init', 'unhook_catcheverest_functions' );

//Responsive Meta in Child Theme 
function catcheverest_child_responsive() {
	// Getting data from Theme Options
	global $catcheverest_options_settings;
   	$options = $catcheverest_options_settings;
	$disable_responsive = $options[ 'disable_responsive' ];
	
	if ( $disable_responsive == "0" ) {	
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
	}
}
add_action( 'wp_head', 'catcheverest_child_responsive', 5 );
