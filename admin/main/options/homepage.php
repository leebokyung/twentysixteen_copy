<?php
/* ----------------------------------------------------------------------------------
	ENABLE HOMEPAGE SLIDER
---------------------------------------------------------------------------------- */

// Content for slider layout - Standard
function thinkup_input_sliderhomepage() {

    global $sir_comm_global;

    $thinkup_homepage_sliderpage1 = $sir_comm_global['main_sliderpage1'];
    $thinkup_homepage_sliderpage2 = $sir_comm_global['main_sliderpage2'];
    $thinkup_homepage_sliderpage3 = $sir_comm_global['main_sliderpage3'];

	// Get url of featured images in slider pages
	$slide1_image_url = wp_get_attachment_url( get_post_thumbnail_id( $thinkup_homepage_sliderpage1 ) );
	$slide2_image_url = wp_get_attachment_url( get_post_thumbnail_id( $thinkup_homepage_sliderpage2 ) );
	$slide3_image_url = wp_get_attachment_url( get_post_thumbnail_id( $thinkup_homepage_sliderpage3 ) );

	// Get titles of slider pages
	$slide1_title = get_the_title( $thinkup_homepage_sliderpage1 );
	$slide2_title = get_the_title( $thinkup_homepage_sliderpage2 );
	$slide3_title = get_the_title( $thinkup_homepage_sliderpage3 );
	
	// Get descriptions (excerpt) of slider pages
	$slide1_description = apply_filters( 'the_excerpt', get_post_field( 'post_excerpt', $thinkup_homepage_sliderpage1 ) );
	$slide2_description = apply_filters( 'the_excerpt', get_post_field( 'post_excerpt', $thinkup_homepage_sliderpage2 ) );
	$slide3_description = apply_filters( 'the_excerpt', get_post_field( 'post_excerpt', $thinkup_homepage_sliderpage3 ) );

	// Get url of slider pages
	$slide1_url = get_permalink( $thinkup_homepage_sliderpage1 );
	$slide2_url = get_permalink( $thinkup_homepage_sliderpage2 );
	$slide3_url = get_permalink( $thinkup_homepage_sliderpage3 );

	// Create array for slider content
	$thinkup_homepage_sliderpage = array( 
		array(
			'slide_id'          => $thinkup_homepage_sliderpage1,
			'slide_image_url'   => $slide1_image_url,
			'slide_title'       => $slide1_title,
			'slide_description' => $slide1_description,
			'slide_url'         => $slide1_url 
		),
		array( 
			'slide_id'          => $thinkup_homepage_sliderpage2, 
			'slide_image_url'   => $slide2_image_url, 
			'slide_title'       => $slide2_title, 
			'slide_description' => $slide2_description, 
			'slide_url'         => $slide2_url 
		),
		array( 
			'slide_id'          => $thinkup_homepage_sliderpage3, 
			'slide_image_url'   => $slide3_image_url, 
			'slide_title'       => $slide3_title, 
			'slide_description' => $slide3_description, 
			'slide_url'         => $slide3_url 
		),
	);

	foreach ($thinkup_homepage_sliderpage as $slide) {

		if ( is_numeric( $slide['slide_id'] ) ) {

			// Get url of background image or set video overlay image
			$slide_image = 'background: url(' . esc_url( $slide['slide_image_url'] ) . ') no-repeat center; background-size: cover;';

			// Used for slider image alt text
			if ( ! empty( $slide['slide_title'] ) ) {
				$slide_alt = esc_attr( $slide['slide_title'] );
			} else {
				$slide_alt = esc_attr__( 'Slider Image', 'grow' );
			}

			echo '<li>',
				 '<a href="'.esc_url( $slide['slide_url'] ).'"><img src="' . get_template_directory_uri() . '/img/transparent.png" style="' . $slide_image . '" class="transparent_img" alt="' . $slide_alt . '" /></a>',
				  '</li>';
		}
	}
}

// Add Slider - Homepage
function thinkup_input_sliderhome() {
    global $sir_comm_global;
    
    /*
    echo "<pre>";
    print_r( $sir_comm_global );
    echo "</pre>";
    exit;
    */


    $main_sliderswitch = isset( $sir_comm_global['main_slider_switch'] ) ? $sir_comm_global['main_slider_switch'] : '';

    $thinkup_homepage_sliderpage1 = $sir_comm_global['main_sliderpage1'];
    $thinkup_homepage_sliderpage2 = $sir_comm_global['main_sliderpage2'];
    $thinkup_homepage_sliderpage3 = $sir_comm_global['main_sliderpage3'];
    $thinkup_homepage_slidername = $sir_comm_global['main_slider_name'];

global $thinkup_homepage_sliderswitch;
/*
global $thinkup_homepage_sliderpage1;
global $thinkup_homepage_sliderpage2;
global $thinkup_homepage_sliderpage3;
*/

$thinkup_class_fullwidth = NULL;
$slide_image             = NULL;
$slider_default          = NULL;

	if ( is_front_page() ) {

        $slider_default .= '<li><a href="#"><img src="'.get_template_directory_uri().'/img/banner01.png" alt="" /></a></li>';
        $slider_default .= '<li><a href="#"><img src="'.get_template_directory_uri().'/img/banner01.png" alt="" /></a></li>';
        $slider_default .= '<li><a href="#"><img src="'.get_template_directory_uri().'/img/banner01.png" alt="" /></a></li>';

		if ( $main_sliderswitch == 'option1' ) {

			echo '<div id="idx_banner"><h2>이벤트 및 광고 배너</h2>';
			echo '<ul class="bxslider">';
				echo $slider_default;
			echo '</ul>';
			echo '</div>';

		} else if ( $main_sliderswitch == 'option2' ) {

			echo '<div id="idx_banner"><h2>이벤트 및 광고 배너</h2>';
			echo do_shortcode( esc_html( $thinkup_homepage_slidername ) );
			echo '</div>';

		} else if ( $main_sliderswitch == 'option3' ) {

			echo '';

		} else if ( $main_sliderswitch == 'option4' ) {

			// Check if page slider has been set
			if( ! is_numeric( $thinkup_homepage_sliderpage1 ) and ! is_numeric( $thinkup_homepage_sliderpage2 ) and ! is_numeric( $thinkup_homepage_sliderpage3 ) ) {

                echo '<div id="idx_banner"><h2>이벤트 및 광고 배너</h2>';
                echo '<ul class="bxslider">';
					echo $slider_default;
                echo '</ul>';
                echo '</div>';
			} else {

                echo '<div id="idx_banner"><h2>이벤트 및 광고 배너</h2>';
                echo '<ul class="bxslider">';
					thinkup_input_sliderhomepage();
                echo '</ul>';
                echo '</div>';
				
			}
		}
	}
}

//----------------------------------------------------------------------------------
//	ENABLE HOMEPAGE CONTENT
//----------------------------------------------------------------------------------

function thinkup_input_homepagesection() {
    global $sir_comm_global;

    $main_sectionswitch = isset( $sir_comm_global['main_sectionswitch'] ) ? (int) $sir_comm_global['main_sectionswitch'] : 0;

	// Output featured content areas
	if ( is_front_page() ) {    // 전면 페이지이면

		//if ( ( current_user_can( 'edit_theme_options' ) and empty( $main_sectionswitch ) ) or $main_sectionswitch == '1' ) {

        if ( $main_sectionswitch == '1' ) {
                $shows = apply_filters('sir_comm_idx_shortcut', array(
                    0 => array(
                    'class'=>$sir_comm_global['main_section1_icon'],
                    'link'=> $sir_comm_global['main_section1_link'] ? $sir_comm_global['main_section1_link'] : '#',
                    'link_target'=> $sir_comm_global['main_section1_link_target'],
                    'title'=>$sir_comm_global['main_section1_title'],
                    ),
                    1 => array(
                    'class'=>$sir_comm_global['main_section2_icon'],
                    'link'=> $sir_comm_global['main_section2_link'] ? $sir_comm_global['main_section2_link'] : '#',
                    'link_target'=> $sir_comm_global['main_section2_link_target'],
                    'title'=>$sir_comm_global['main_section2_title'],
                    ),
                    2 => array(
                    'class'=>$sir_comm_global['main_section3_icon'],
                    'link'=> $sir_comm_global['main_section3_link'] ? $sir_comm_global['main_section3_link'] : '#',
                    'link_target'=> $sir_comm_global['main_section3_link_target'],
                    'title'=>$sir_comm_global['main_section3_title'],
                    ),
                    3 => array(
                    'class'=>$sir_comm_global['main_section4_icon'],
                    'link'=> $sir_comm_global['main_section4_link'] ? $sir_comm_global['main_section4_link'] : '#',
                    'link_target'=> $sir_comm_global['main_section4_link_target'],
                    'title'=>$sir_comm_global['main_section4_title'],
                    ),
                    4 => array(
                    'class'=>$sir_comm_global['main_section5_icon'],
                    'link'=> $sir_comm_global['main_section5_link'] ? $sir_comm_global['main_section5_link'] : '#',
                    'link_target'=> $sir_comm_global['main_section5_link_target'],
                    'title'=>$sir_comm_global['main_section5_title'],
                    ),
                    5 => array(
                    'class'=>$sir_comm_global['main_section6_icon'],
                    'link'=> $sir_comm_global['main_section6_link'] ? $sir_comm_global['main_section6_link'] : '#',
                    'link_target'=> $sir_comm_global['main_section6_link_target'],
                    'title'=>$sir_comm_global['main_section6_title'],
                    ),
                    6 => array(
                    'class'=>$sir_comm_global['main_section7_icon'],
                    'link'=> $sir_comm_global['main_section7_link'] ? $sir_comm_global['main_section7_link'] : '#',
                    'link_target'=> $sir_comm_global['main_section7_link_target'],
                    'title'=>$sir_comm_global['main_section7_title'],
                    ),
                    7 => array(
                    'class'=>$sir_comm_global['main_section8_icon'],
                    'link'=> $sir_comm_global['main_section8_link'] ? $sir_comm_global['main_section8_link'] : '#',
                    'link_target'=> $sir_comm_global['main_section8_link_target'],
                    'title'=>$sir_comm_global['main_section8_title'],
                    ),
                    8 => array(
                    'class'=>$sir_comm_global['main_section9_icon'],
                    'link'=> $sir_comm_global['main_section9_link'] ? $sir_comm_global['main_section9_link'] : '#',
                    'link_target'=> $sir_comm_global['main_section9_link_target'],
                    'title'=>$sir_comm_global['main_section9_title'],
                    ),
                    9 => array(
                    'class'=>$sir_comm_global['main_section10_icon'],
                    'link'=> $sir_comm_global['main_section10_link'] ? $sir_comm_global['main_section10_link'] : '#',
                    'link_target'=> $sir_comm_global['main_section10_link_target'],
                    'title'=>$sir_comm_global['main_section10_title'],
                    ),
                ));
        ?>

        <div id="idx_shortcut">
            <ul>
                <?php for($i=0;$i<=4;$i++){ ?>
                <li class="<?php echo esc_attr($shows[$i]['class']); ?>"><a href="<?php echo esc_url($shows[$i]['link']); ?>" target="<?php echo esc_attr($shows[$i]['link_target']); ?>"><span><?php echo esc_html($shows[$i]['title']); ?></span></a></li>
                <?php } //end for ?>
            </ul> 
            <ul class="ul-margin">
                <?php for($i=5;$i<=9;$i++){ ?>
                <li class="<?php echo esc_attr($shows[$i]['class']); ?>"><a href="<?php echo esc_url($shows[$i]['link']); ?>" target="<?php echo esc_attr($shows[$i]['link_target']); ?>"><span><?php echo esc_html($shows[$i]['title']); ?></span></a></li>
                <?php } //end for ?>
            </ul>
        </div>

        <?php
        
		}
	}
}

?>