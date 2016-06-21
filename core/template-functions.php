<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Content for slider layout - Standard
function sircomm_input_sliderhomepage() {

    $sircomm_homepage_sliderpage1 = summeripha_get_option('sircomm_homepage_sliderpage1', '');
    $sircomm_homepage_sliderpage2 = summeripha_get_option('sircomm_homepage_sliderpage2', '');
    $sircomm_homepage_sliderpage3 = summeripha_get_option('sircomm_homepage_sliderpage3', '');

	// Get url of featured images in slider pages
	$slide1_image_url = wp_get_attachment_url( get_post_thumbnail_id( $sircomm_homepage_sliderpage1 ) );
	$slide2_image_url = wp_get_attachment_url( get_post_thumbnail_id( $sircomm_homepage_sliderpage2 ) );
	$slide3_image_url = wp_get_attachment_url( get_post_thumbnail_id( $sircomm_homepage_sliderpage3 ) );

	// Get titles of slider pages
	$slide1_title = get_the_title( $sircomm_homepage_sliderpage1 );
	$slide2_title = get_the_title( $sircomm_homepage_sliderpage2 );
	$slide3_title = get_the_title( $sircomm_homepage_sliderpage3 );
	
	// Get descriptions (excerpt) of slider pages
	$slide1_description = apply_filters( 'the_excerpt', get_post_field( 'post_excerpt', $sircomm_homepage_sliderpage1 ) );
	$slide2_description = apply_filters( 'the_excerpt', get_post_field( 'post_excerpt', $sircomm_homepage_sliderpage2 ) );
	$slide3_description = apply_filters( 'the_excerpt', get_post_field( 'post_excerpt', $sircomm_homepage_sliderpage3 ) );

	// Get url of slider pages
	$slide1_url = get_permalink( $sircomm_homepage_sliderpage1 );
	$slide2_url = get_permalink( $sircomm_homepage_sliderpage2 );
	$slide3_url = get_permalink( $sircomm_homepage_sliderpage3 );

	// Create array for slider content
	$sircomm_homepage_sliderpage = array( 
		array(
			'slide_id'          => $sircomm_homepage_sliderpage1,
			'slide_image_url'   => $slide1_image_url,
			'slide_title'       => $slide1_title,
			'slide_description' => $slide1_description,
			'slide_url'         => $slide1_url 
		),
		array( 
			'slide_id'          => $sircomm_homepage_sliderpage2, 
			'slide_image_url'   => $slide2_image_url, 
			'slide_title'       => $slide2_title, 
			'slide_description' => $slide2_description, 
			'slide_url'         => $slide2_url 
		),
		array( 
			'slide_id'          => $sircomm_homepage_sliderpage3, 
			'slide_image_url'   => $slide3_image_url, 
			'slide_title'       => $slide3_title, 
			'slide_description' => $slide3_description, 
			'slide_url'         => $slide3_url 
		),
	);

	foreach ($sircomm_homepage_sliderpage as $slide) {

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

function sircomm_input_sliderhome() {
    $main_sliderswitch = summeripha_get_option("sircomm_homepage_sliderswitch", 'option4');

    $sircomm_homepage_sliderpage1 = summeripha_get_option('sircomm_homepage_sliderpage1', '');
    $sircomm_homepage_sliderpage2 = summeripha_get_option('sircomm_homepage_sliderpage2', '');
    $sircomm_homepage_sliderpage3 = summeripha_get_option('sircomm_homepage_sliderpage3', '');
    $sircomm_homepage_slidername = summeripha_get_option('sircomm_homepage_slidername', '');

    $slider_default = '';

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
			echo do_shortcode( sanitize_text_field( $sircomm_homepage_slidername ) );
			echo '</div>';

		} else if ( $main_sliderswitch == 'option3' ) {

			echo '';

		} else if ( $main_sliderswitch == 'option4' ) {

			// Check if page slider has been set
			if( ! is_numeric( $sircomm_homepage_sliderpage1 ) and ! is_numeric( $sircomm_homepage_sliderpage2 ) and ! is_numeric( $sircomm_homepage_sliderpage3 ) ) {

                echo '<div id="idx_banner"><h2>이벤트 및 광고 배너</h2>';
                echo '<ul class="bxslider">';
					echo $slider_default;
                echo '</ul>';
                echo '</div>';
			} else {

                echo '<div id="idx_banner"><h2>이벤트 및 광고 배너</h2>';
                echo '<ul class="bxslider">';
					sircomm_input_sliderhomepage();
                echo '</ul>';
                echo '</div>';
				
			}
		}
	}
}

function sircomm_input_homepagesection() {

    $sircomm_homepage_icon_switch = summeripha_get_option('sircomm_homepage_icon_switch', 'switch_on');

    $main_sectionswitch = true;

    if( ! $sircomm_homepage_icon_switch || $sircomm_homepage_icon_switch == 'switch_off' ){
        $main_sectionswitch = false;
    }

	// Output featured content areas
	if ( is_front_page() ) {    // 전면 페이지이면

        if ( $main_sectionswitch ) {

            $datas = array();

            $tmp_array = summeripha_get_var_by();
            $tmp_keys_array = array_keys($tmp_array);

            $tmp_texts = summeripha_get_var_by('icon_text');

            for($i=0;$i<10;$i++){

                $j = $i + 1;

                $datas[$i] = array(
                    'class' =>  summeripha_get_option('sircomm_homepage_section'.$j.'_icon', $tmp_keys_array[$i]),
                    'link'  =>  summeripha_get_option('sircomm_homepage_section'.$j.'_link', '#'),
                    'link_target'   =>  summeripha_get_option('sircomm_homepage_section'.$j.'_link_target', '_self'),
                    'title' => summeripha_get_option('sircomm_homepage_section'.$j.'_title', $tmp_texts[$i]), 
                    );
            }

            $shows = apply_filters('sir_comm_idx_shortcut', $datas);
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