<?php
/**
 * Theme setup functions.
 *
 * @package ThinkUpThemes
 */


/* ----------------------------------------------------------------------------------
	ADD CUSTOM VARIABLES
---------------------------------------------------------------------------------- */

/* Add global variables used in Redux framework */
function sir_comm_set_reduxvariables() { 

	// Fetch options stored in $data.
	global $sir_comm_redux_variables, $sir_comm_global;

    if( ! function_exists('sir_comm_get_var') ){
        return;
    }

    $tmp_array = sir_comm_get_var_by();
    $tmp_keys_array = array_keys($tmp_array);

    $tmp_texts = sir_comm_get_var_by('icon_text');
    $link_targets = sir_comm_get_var_by('link_target');

    //값 초기화
    if( !isset($sir_comm_redux_variables['thinkup_homepage_sliderswitch']) ){
        $sir_comm_redux_variables['thinkup_homepage_sliderswitch'] = 'option1';
    }

    if( !isset($sir_comm_redux_variables['thinkup_homepage_sectionswitch']) ){
        $sir_comm_redux_variables['thinkup_homepage_sectionswitch'] = '1';
    }

    for($i=1;$i<11;$i++){
        $j = $i -1;

        if( ! isset( $sir_comm_redux_variables['thinkup_homepage_section'.$i.'_icon'] ) ){
            $sir_comm_redux_variables['thinkup_homepage_section'.$i.'_icon'] = $tmp_keys_array[$j];
        }

        if( ! isset( $sir_comm_redux_variables['thinkup_homepage_section'.$i.'_title'] ) ){
            $sir_comm_redux_variables['thinkup_homepage_section'.$i.'_title'] = $tmp_texts[$j];
        }

        if( ! isset( $sir_comm_redux_variables['thinkup_homepage_section'.$i.'_link'] ) ){
            $sir_comm_redux_variables['thinkup_homepage_section'.$i.'_link'] = '#';
        }
    }


    // slider
    $sir_comm_global['main_slider_switch']  = sir_comm_get_var ( 'thinkup_homepage_sliderswitch' );
    $sir_comm_global['main_slider_name']  = sir_comm_get_var ( 'thinkup_homepage_slidername' );
    $sir_comm_global['main_sliderpage1']  = sir_comm_get_var ( 'thinkup_homepage_sliderpage1' );
    $sir_comm_global['main_sliderpage2']  = sir_comm_get_var ( 'thinkup_homepage_sliderpage2' );
    $sir_comm_global['main_sliderpage3']  = sir_comm_get_var ( 'thinkup_homepage_sliderpage3' );

	//  main icon
	$sir_comm_global['main_sectionswitch']              = sir_comm_get_var ( 'thinkup_homepage_sectionswitch' );
    for($i=1;$i<11;$i++){
        $sir_comm_global['main_section'.$i.'_icon'] = sir_comm_get_var ( 'thinkup_homepage_section'.$i.'_icon' );
        $sir_comm_global['main_section'.$i.'_title'] = sir_comm_get_var ( 'thinkup_homepage_section'.$i.'_title' );
        $sir_comm_global['main_section'.$i.'_link'] = sir_comm_get_var ( 'thinkup_homepage_section'.$i.'_link' );
        $sir_comm_global['main_section'.$i.'_link_target'] = sir_comm_get_var ( 'thinkup_homepage_section'.$i.'_link_target' );
    }
}
add_action( 'sir_comm_before_header', 'sir_comm_set_reduxvariables' );

?>