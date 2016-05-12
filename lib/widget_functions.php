<?php

define('SIR_CMM_NAME', 'sir_community');

require get_template_directory() . '/lib/hooks.php';
require get_template_directory() . '/classes/gnucommerce_widget.php';

/**
 * Register our sidebars and widgetized areas.
 *
 */
function sir_community_widgets_init() {

    register_widget( 'sir_latest_board_widget' );

    register_sidebar( array(
        'name'          => __( '일반 최신글 50% 영역', SIR_CMM_NAME ),
        'id'            => 'main-latest-50pro',
        'description' 		=> __('일반 최신글 50% 영역', SIR_CMM_NAME ),
        'before_widget' => '<div class="main-latest-50pro"><div class="inner">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="sir_comm_title">',
        'after_title'   => '</h2>',
    ));

    /*
    $main_latest_widget_regions = apply_filters( 'sir_community_main_latest_widget_regions', 4 );
    for ( $i = 1; $i <= intval( $main_latest_widget_regions ); $i++ ) {
        register_sidebar( array(
            'name'          => sprintf( __( '일반 최신글 %d', SIR_CMM_NAME ), $i ),
            'id'            => sprintf( 'main-latest-%d', $i ),
            'description' 		=> sprintf( __( '위젯 최신글 영역 %d.', SIR_CMM_NAME ), $i ),
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
        ));
    }
    */

    register_sidebar( array(
        'name'          => __( '일반 최신글 이미지 영역', SIR_CMM_NAME ),
        'id'            => 'main-gallery-latest',
        'description' 		=> __('위젯 최신글 이미지 영역', SIR_CMM_NAME ),
        'before_widget' => '<div class="main-latest-area">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="sir_comm_title">',
        'after_title'   => '</h2>',
    ));
}

add_action( 'widgets_init', 'sir_community_widgets_init' );

?>