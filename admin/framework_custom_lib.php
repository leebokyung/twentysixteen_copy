<?php
//Redux Framework custom function

if ( ! class_exists( 'Redux' ) ) {
    return;
}

// Assign custom function to fetch variable values
if ( !function_exists( 'sir_comm_get_var' ) ) :
function sir_comm_get_var( $name, $key = false ) {
  global $sir_comm_redux_variables;
  $options = $sir_comm_redux_variables;

  // Set this to your preferred default value
  $var = '';

  if ( empty( $name ) && !empty( $options ) ) :
    $var = $options;
  else :
    if ( !empty( $options[$name] ) ) :
      if ( !empty( $key ) && !empty( $options[$name][$key] ) && $key !== true ) :
        $var = $options[$name][$key];
      elseif (  !empty( $key ) && empty( $options[$name][$key] ) && $key !== true  ) :
        $var = '0';
      else :
        $var = $options[$name];
      endif;
    endif;
  endif;

  return $var;
}
endif;

function sir_comm_get_var_by($key='icon_class'){

    if( $key == 'icon_class' ){
        return array(
                    'sc_notice'=>'sc_notice',
                    'sc_latest'=>'sc_latest',
                    'sc_g5'=>'sc_g5',
                    'sc_yc5'=>'sc_yc5',
                    'sc_data'=>'sc_data',
                    'sc_gallery'=>'sc_gallery',
                    'sc_nquiryt'=>'sc_nquiryt',
                    'sc_contact'=>'sc_contact',
                    'sc_tip'=>'sc_tip',
                    'sc_customer'=>'sc_customer'
                );
    } else if( $key == 'link_target' ){
        return array(
                    '_self'=>'_self',
                    '_blank'=>'_blank',
                );
    } else if( $key == 'icon_text' ){
        return array(
            0=> __('공지', SIR_CMM_NAME),
            1=> __('최근글', SIR_CMM_NAME),
            2=> __('그누보드5', SIR_CMM_NAME),
            3=> __('영카트5', SIR_CMM_NAME),
            4=> __('회원자료', SIR_CMM_NAME),
            5=> __('갤러리', SIR_CMM_NAME),
            6=> __('1:1문의', SIR_CMM_NAME),
            7=> __('오시는 길', SIR_CMM_NAME),
            8=> __('강좌/팁', SIR_CMM_NAME),
            9=> __('고객센터', SIR_CMM_NAME),
            );
    }
}

function sir_comm_set_option_redux($key='icon'){
    if( $key == 'icon' ){
        
        $tmp_array = sir_comm_get_var_by();
        $tmp_keys_array = array_keys($tmp_array);

        $tmp_texts = sir_comm_get_var_by('icon_text');
        $link_targets = sir_comm_get_var_by('link_target');

        $icon_options = array();
        
		$icon_options[] = array(
				'title'                      => __('메인 화면에 아이콘을 표시합니다.', SIR_CMM_NAME), 
				//$sircomm_subtitle_panel      => '',
				//$sircomm_subtitle_customizer => '',
				'id'                         => 'sircomm_homepage_sectionswitch',
				'type'                       => 'switch',
				'default'                    => '1',
			);

		$icon_options[] = array(
				'id'       => 'sircomm_homepage_section1_icon',
				'title'    => __('메인 아이콘 1', SIR_CMM_NAME),
				'desc'     => __('배경 아이콘을 선택합니다.', SIR_CMM_NAME),
				'type'     => 'select',
				'data'     => '',
                'options'   =>  sir_comm_get_var_by(),
                'default'   =>  'sc_notice',
				'required' => array(
					array( 'sircomm_homepage_sectionswitch', '=', 
						array( '1' ),
					), 
				)
			);

        for( $i=0;$i<10;$i++ ){

            $j = $i + 1;

            $icon_options[] = array(
                    'id'       => 'sircomm_homepage_section'.$j.'_icon',
                    'title' => sprintf( _n( '메인 아이콘 %s', '메인 아이콘 %s', $j, SIR_CMM_NAME ), $j ),
                    'desc'     => __('배경 아이콘을 선택합니다.', SIR_CMM_NAME),
                    'type'     => 'select',
                    'options'   =>  $tmp_array,
                    'default'   =>  $tmp_keys_array[$i],
                    'required' => array(
                        array( 'sircomm_homepage_sectionswitch', '=', 
                            array( '1' ),
                        ), 
                    )
                );

            $icon_options[] = array(
                    'id'       => 'sircomm_homepage_section'.$j.'_title',
                    'desc'     => __('텍스트를 지정합니다.', SIR_CMM_NAME),
                    'type'     => 'text',
                    'validate' => 'html',
                    'default'                    => $tmp_texts[$i],
                    'required' => array( 
                        array( 'sircomm_homepage_sectionswitch', '=', 
                            array( '1' ),
                        ), 
                    )
                );

            $icon_options[] = 			array(
				'id'       => 'sircomm_homepage_section'.$j.'_link',
				'desc'     => __('링크 할 주소( url )', SIR_CMM_NAME), 
				'type'     => 'text',
				'validate' => 'html',
				'required' => array( 
					array( 'sircomm_homepage_sectionswitch', '=', 
						array( '1' ),
					), 
				)
			);

            $icon_options[] =			array(
				'id'       => 'sircomm_homepage_section'.$j.'_link_target',
				'desc'     => __('링크 타겟', SIR_CMM_NAME),
				'type'     => 'select',
                'options'   =>  $link_targets,
                'default'   =>  '_self',
				'required' => array( 
					array( 'sircomm_homepage_sectionswitch', '=', 
						array( '1' ),
					), 
				)
			);

        }

        return $icon_options;
    }
}
?>