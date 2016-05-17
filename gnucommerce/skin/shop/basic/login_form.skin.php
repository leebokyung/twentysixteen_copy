<?php
if( !defined('GC_NAME') ) exit;

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>
<div class="gc_login_form">
    <h2>회원로그인 안내</h2>
    <?php do_action( GC_NAME.'_login_form_start' ); ?>
    <?php
    $args = apply_filters( GC_NAME.'_login_form_filter', array(
        'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
        'form_id' => 'gc_loginform',
        'label_username' => __('사용자명', GC_NAME),
        'label_password' => __('비밀번호', GC_NAME),
        'label_remember' => __('기억하기', GC_NAME),
        'label_log_in' => __('로그인', GC_NAME),
        'remember' => true
    ));
    ob_start();
    wp_login_form( $args );
    echo apply_filters( GC_NAME.'_login_form_html', ob_get_clean() );
    ?>
    <?php do_action( GC_NAME.'_login_form_end' ); ?>
</div>