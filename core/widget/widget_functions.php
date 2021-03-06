<?php
if ( ! defined( 'ABSPATH' ) ) exit;

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
        'name'          => __( '메인 1 영역', 'gnucommerce-2016-summer-ipha' ),
        'id'            => 'main-head-latest',
        'description' 		=> __('사이트 메인에만 적용됩니다.', 'gnucommerce-2016-summer-ipha' ),
        'before_widget' => '<div class="widget widget_latest">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="sir_comm_title widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar( array(
        'name'          => __( '메인 2 영역', 'gnucommerce-2016-summer-ipha' ),
        'id'            => 'main-latest-50pro',
        'description' 		=> __('사이트 메인에만 적용됩니다.', 'gnucommerce-2016-summer-ipha' ),
        'before_widget' => '<div class="main-latest-50pro"><div class="inner">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="sir_comm_title widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar( array(
        'name'          => __( '메인 3 영역', 'gnucommerce-2016-summer-ipha' ),
        'id'            => 'main-gallery-latest',
        'description' 		=> __('사이트 메인에만 적용됩니다.', 'gnucommerce-2016-summer-ipha' ),
        'before_widget' => '<div class="main-latest-area">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="sir_comm_title widget-title">',
        'after_title'   => '</h2>',
    ));
}
?>