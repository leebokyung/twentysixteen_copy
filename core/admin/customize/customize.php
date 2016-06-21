<?php

if (!function_exists('summeripha_customize_panel_function')) {

	function summeripha_customize_panel_function() {
		
		$theme_panel = array ( 

			array( 
				"title" => __( 'Theme Option', 'gnucommerce-2016-summer-ipha'),
				"description" => __( 'Theme Option', 'gnucommerce-2016-summer-ipha'),
				"type" => "panel",
				"id" => "summeripha_theme",
				"priority" => "09",
			),

			array(
				"title" => __( "Slider Setting", 'gnucommerce-2016-summer-ipha'),
                'description' => __( 'Control Slider Setting', 'gnucommerce-2016-summer-ipha'),
				"type" => "section",
				"panel" => "summeripha_theme",
				"priority" => "10",
				"id" => "slideshow_settings",
			),

			array(

				'label' => __( 'Choose Homepage Slider', 'gnucommerce-2016-summer-ipha'),
                'description' => __( 'Switch on to enable home page slider.', 'gnucommerce-2016-summer-ipha'),
				'type'  => 'button_set',
				'id' => 'sircomm_homepage_sliderswitch',
				'options'                    => array(
					'option4' => 'Page Slider',
					'option2' => 'Custom Slider',
					'option3' => 'Disable'
				),
				'section' => 'slideshow_settings',
				'std' => 'option4',

			),

			array(
				'label'                      => __('Homepage Slider Shortcode', 'gnucommerce-2016-summer-ipha'), 
				'description'      => __('Input the shortcode of the slider you want to display. [shortcode_name].', 'gnucommerce-2016-summer-ipha'),
				'id'                         => 'sircomm_homepage_slidername',
				'type'                       => 'text',
                'section' => 'slideshow_settings',
				'validate'                   => 'esc_html',
                'std'   =>  '',
			),

			array(

				'label' => __( "Slide #1", 'gnucommerce-2016-summer-ipha'),
				'description' => '',
				"id" => "sircomm_homepage_sliderpage1",
				'type' => 'select',
				'section' => 'slideshow_settings',
                'options'  =>  summeripha_get_wordpress_data('pages'),
				"std" => '',
			),

			array( 

				"label" => __( "Slide #2", 'gnucommerce-2016-summer-ipha'),
				"description" => '',
				"id" => "sircomm_homepage_sliderpage2",
				'type' => 'select',
				"section" => "slideshow_settings",
                'options'  =>  summeripha_get_wordpress_data('pages'),
				"std" => '',
			),

			array( 

				"label" => __( "Slide #3", 'gnucommerce-2016-summer-ipha'),
				"description" => '',
				"id" => "sircomm_homepage_sliderpage3",
				'type' => 'select',
				"section" => "slideshow_settings",
                'options'  =>  summeripha_get_wordpress_data('pages'),
				"std" => '',
			),


			array(
				"title" => __( "Main Icon Setting", 'gnucommerce-2016-summer-ipha'),
                'description' => __( 'Control Main icon Setting', 'gnucommerce-2016-summer-ipha'),
				"type" => "section",
				"panel" => "summeripha_theme",
				"priority" => "10",
				"id" => "main_icon_settings",
			),

			array(

				'label' => __( 'Display icon to main', 'gnucommerce-2016-summer-ipha'),
                'description' => __( 'Switch on to enable main icon.', 'gnucommerce-2016-summer-ipha'),
				'type'  => 'button_set',
				'id' => 'sircomm_homepage_icon_switch',
				'options'                    => array(
					'switch_on' => 'On',
					'switch_off' => 'Off',
				),
				'section' => 'main_icon_settings',
				'std' => 'switch_on',

			),

        );

        $tmp_array = summeripha_get_var_by();
        $tmp_keys_array = array_keys($tmp_array);

        $tmp_texts = summeripha_get_var_by('icon_text');
        $link_targets = summeripha_get_var_by('link_target');

        for( $i=0;$i<10;$i++ ){

            $j = $i + 1;

            $theme_panel[] = array(
                    'id'       => 'sircomm_homepage_section'.$j.'_icon',
                    'label' => sprintf( _n( 'Main Icon %s', 'Main Icon %s', $j, 'gnucommerce-2016-summer-ipha' ), $j ),
                    'description'     => __('Choose Background Icon.', 'gnucommerce-2016-summer-ipha'),
                    'type'     => 'select',
                    'options'   =>  $tmp_array,
                    'section' => 'main_icon_settings',
                    'std' => $tmp_keys_array[$i],
                );

            $theme_panel[] = array(
                    'id'       => 'sircomm_homepage_section'.$j.'_title',
                    'description'     => __('Specifies the text', 'gnucommerce-2016-summer-ipha'),
                    'type'     => 'text',
                    'validate' => 'esc_html',
                    'std'                    => $tmp_texts[$i],
                    'section' => 'main_icon_settings',
                );

            $theme_panel[] = 			array(
				'id'       => 'sircomm_homepage_section'.$j.'_link',
				'description'     => __('Link URL', 'gnucommerce-2016-summer-ipha'), 
				'type'     => 'text',
				'validate' => 'esc_html',
				'section' => 'main_icon_settings',
			);

            $theme_panel[] =			array(
				'id'       => 'sircomm_homepage_section'.$j.'_link_target',
				'description'     => __('Link target', 'gnucommerce-2016-summer-ipha'),
				'type'     => 'select',
                'options'   =>  $link_targets,
                'std'   =>  '_self',
				'section' => 'main_icon_settings',
			);

        }
		
		new summeripha_customize($theme_panel);
		
	} 
	
	add_action( 'summeripha_customize_panel', 'summeripha_customize_panel_function', 10, 2 );

}

do_action('summeripha_customize_panel');

?>