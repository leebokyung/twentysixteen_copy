<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class sir_comm_login_widget extends SIR_COMM_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'sir_comm widget_login';
		$this->widget_description = __( '로그인 위젯입니다.', SIR_CMM_NAME );
		$this->widget_id          = 'sir_comm_login';
		$this->widget_name        = __( '로그인 위젯', SIR_CMM_NAME );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( '로그인 위젯', SIR_CMM_NAME ),
				'label' => __( '제목', SIR_CMM_NAME )
			),
            );

        parent::__construct();
    }


    public function print_login_form( $args = array() ){

        $defaults = array(
            'echo' => true,
            // Default 'redirect' value takes the user back to the request URI.
            'redirect' => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'form_id' => 'loginform',
            'id_username' => 's-id',
            'id_password' => 's-pw',
            'id_remember' => 'rememberme',
            'id_submit' => 's-login',
            'remember' => true,
            'value_username' => '',
            // Set 'value_remember' to true to default the "Remember me" checkbox to checked.
            'value_remember' => false,
        );

        $args = wp_parse_args( $args, apply_filters( 'sir_comm_login_form_defaults', $defaults ) );

    ?>
        <div id="side-login">
            <?php
            if( is_user_logged_in() ){      //로그인 했다면
                $user_id      = get_current_user_id();
                $current_user = wp_get_current_user();

                if ( current_user_can( 'read' ) ) {
                    $profile_url = get_edit_profile_url( $user_id );
                } elseif ( is_multisite() ) {
                    $profile_url = get_dashboard_url( $user_id, 'profile.php' );
                } else {
                    $profile_url = false;
                }

                $avatar = get_avatar( $user_id, 26 );   // 회원 프로필 이미지 사이즈
            ?>
            <div class="avatar">
                <?php echo $avatar; ?>
                <?php
                /* 회원 별명 출력 */
                echo esc_attr($current_user->display_name);
                ?>
            </div>
            <div class="avatar-info">
                <a href="<?php echo $profile_url; ?>" class="my-profile"><?php _e('내 프로필 편집', SIR_CMM_NAME); ?></a>
                <a href="<?php echo wp_logout_url(); ?>"><?php _e('로그아웃', SIR_CMM_NAME); ?></a>
            </div>

            <?php } else {  //로그인 하지 않았다면 ?>
            <form name="<?php echo $args['form_id']; ?> id="<?php echo $args['form_id']; ?>" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) );?>" method="post">
                <?php echo '<input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />'; ?>
                <fieldset>
                    <legend><i class="fa fa-sign-in" aria-hidden="true"></i> <?php _e('로그인', SIR_CMM_NAME);?></legend>
                    <div id="side-login-input">
                        <label for="<?php echo esc_attr( $args['id_username'] ); ?>" class="screen-reader-text"><?php _e('아이디', SIR_CMM_NAME);?></label>
                        <input type="text" name="log" placeholder="<?php _e('아이디', SIR_CMM_NAME);?>" id="<?php echo esc_attr( $args['id_username'] ); ?>"/>
                        <label for="<?php echo esc_attr( $args['id_password'] ); ?>" class="screen-reader-text"><?php _e('비밀번호', SIR_CMM_NAME);?></label>
                        <input type="password" name="pwd" placeholder="<?php _e('비밀번호', SIR_CMM_NAME);?>" id="<?php echo esc_attr( $args['id_password'] ); ?>"/>
                    </div>
                    <input type="submit" name="wp-submit" value="<?php _e('로그인', SIR_CMM_NAME);?>" id="<?php echo esc_attr( $args['id_submit'] ); ?>"/>
                    
                  <div class="login-auto-btn">
                      <input type="checkbox" name="rememberme" value="forever" id="<?php echo esc_attr( $args['id_remember'] );?>" <?php echo $args['value_remember'] ? ' checked="checked"' : ''; ?>/>
                      <label for="<?php echo esc_attr( $args['id_remember'] );?>"><?php _e('자동로그인', SIR_CMM_NAME);?></label>  
                  </div>
                  
                  <p class="login-content-link">
                      <a href="<?php echo wp_lostpassword_url(); ?>"><i class="fa fa-search" aria-hidden="true"></i> <?php _e('정보 찾기', SIR_CMM_NAME);?></a>
                      <span style="color:#fff;">|</span>
                      <a href="<?php echo wp_registration_url(); ?>"><i class="fa fa-heart-o" aria-hidden="true"></i> <?php _e('회원가입', SIR_CMM_NAME);?></a>
                  </p>
                </fieldset>
            </form>
            <?php } ?>
        </div>
    <?php
    }

	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

        $this->print_login_form();

		echo $this->cache_widget( $args, ob_get_clean() );
	}
}
?>