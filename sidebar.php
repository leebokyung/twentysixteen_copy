<?php
/**
 * The template for the sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<?php if ( is_active_sidebar( 'sidebar-1' )  ) : ?>
	<aside id="secondary" class="sidebar widget-area" role="complementary">
	    
	    <div id="side-login">
            <fieldset>
                <legend>로그인</legend>
                <div id="side-login-input">
                    <label for="" class="screen-reader-text">아이디</label>
                    <input type="text" name="some_name" placeholder="아이디" id="s-id"/>
                    <label for="" class="screen-reader-text">비밀번호</label>
                    <input type="text" name="some_name" placeholder="비밀번호" id="s-pw"/>
                </div>
                <input type="submit" name="some_name" value="로그인" id="s-login"/>
                
              <div class="login-auto-btn">
                  <input type="checkbox" name="some_name" value="auto_login" id="some_name"/>
                  <label for="">자동로그인</label>  
              </div>
              
              <p class="login-content-link">
                  <a href="#">정보 찾기</a>
                  <a href="#">회원가입</a>
              </p>
            </fieldset>
        </div>
        
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</aside><!-- .sidebar .widget-area -->
	
<?php endif; ?>