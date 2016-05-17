<?php
/**
 * The template for the sidebar containing the main widget area
 *
 */
?>

<?php if ( is_active_sidebar( 'sidebar-1' )  ) : ?>
	<aside id="secondary" class="sidebar widget-area" role="complementary">
	    
        <?php
        /*
	    <div id="side-login">
            <fieldset>
                <legend><i class="fa fa-sign-in" aria-hidden="true"></i> <?php _e('로그인', SIR_CMM_NAME);?></legend>
                <div id="side-login-input">
                    <label for="" class="screen-reader-text"><?php _e('아이디', SIR_CMM_NAME);?></label>
                    <input type="text" name="some_name" placeholder="아이디" id="s-id"/>
                    <label for="" class="screen-reader-text"><?php _e('비밀번호', SIR_CMM_NAME);?></label>
                    <input type="text" name="some_name" placeholder="비밀번호" id="s-pw"/>
                </div>
                <input type="submit" name="some_name" value="<?php _e('로그인', SIR_CMM_NAME);?>" id="s-login"/>
                
              <div class="login-auto-btn">
                  <input type="checkbox" name="some_name" value="auto_login" id="some_name"/>
                  <label for=""><?php _e('자동로그인', SIR_CMM_NAME);?></label>  
              </div>
              
              <p class="login-content-link">
                  <a href="#"><i class="fa fa-search" aria-hidden="true"></i> <?php _e('정보 찾기', SIR_CMM_NAME);?></a>
                  <span style="color:#fff;">|</span>
                  <a href="#"><i class="fa fa-heart-o" aria-hidden="true"></i> <?php _e('회원가입', SIR_CMM_NAME);?></a>
              </p>
            </fieldset>
        </div>
        */
        ?>
        
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</aside><!-- .sidebar .widget-area -->
	
<?php endif; ?>