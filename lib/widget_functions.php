<?php
require get_template_directory() . '/lib/hooks.php';
require get_template_directory() . '/classes/gnucommerce_widget.php';

add_action( 'widgets_init', 'sir_community_widgets_init' );

/**
 * Register our sidebars and widgetized areas.
 *
 */
function sir_community_widgets_init() {


    if( defined('GC_BOARD_KEY') ){
        register_widget( 'sir_latest_board_widget' );   // 그누커머스 최신글 위젯
        register_widget( 'sir_comm_login_widget' ); // 로그인 위젯
    }

    register_sidebar( array(
        'name'          => __( '메인 1 영역', SIR_CMM_NAME ),
        'id'            => 'main-head-latest',
        'description' 		=> __('사이트 메인에만 적용됩니다.', SIR_CMM_NAME ),
        'before_widget' => '<div class="widget widget_latest">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="sir_comm_title widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar( array(
        'name'          => __( '메인 2 영역', SIR_CMM_NAME ),
        'id'            => 'main-latest-50pro',
        'description' 		=> __('사이트 메인에만 적용됩니다.', SIR_CMM_NAME ),
        'before_widget' => '<div class="main-latest-50pro"><div class="inner">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="sir_comm_title widget-title">',
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
        'name'          => __( '메인 3 영역', SIR_CMM_NAME ),
        'id'            => 'main-gallery-latest',
        'description' 		=> __('사이트 메인에만 적용됩니다.', SIR_CMM_NAME ),
        'before_widget' => '<div class="main-latest-area">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="sir_comm_title widget-title">',
        'after_title'   => '</h2>',
    ));
}
?>