<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since sir community 1.0
 */
?>

		</div><!-- .site-content -->

		<footer id="colophon" class="site-footer" role="contentinfo">
		    <div class="foot-inner"><!-- site-inner -->
    			<?php if ( has_nav_menu( 'social' ) ) : ?>
    				<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Social Links Menu', 'twentysixteen' ); ?>">
    					<?php
    						wp_nav_menu( array(
    							'theme_location' => 'social',
    							'menu_class'     => 'social-links-menu',
    							'depth'          => 1,
    							'link_before'    => '<span class="screen-reader-text">',
    							'link_after'     => '</span>',
    						) );
    					?>
    				</nav><!-- .social-navigation -->
    			<?php endif; ?>
    
    			<div class="site-info">
    			    <div id="footer-info">
    			        <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>   
    			        <address class="f-info footer-address">서울특별시 강남구 강남대로 123, 역삼동 1004호</address>
    			        <span class="f-info footer-phone">T.02-1234-5678</span>
    			        <span class="f-info footer-fax">F.02-1234-5678</span>
    			        <ul class="footer-sns">
    			            <li><a href="#"><i class="fa fa-lg fa-facebook" aria-hidden="true"></i><span class="screen-reader-text">페이스북</span></a></li>
    			            <li><a href="#"><i class="fa fa-lg fa-twitter" aria-hidden="true"></i><span class="screen-reader-text">트위터</span></a></li>
    			            <li><a href="#"><i class="fa fa-lg fa-instagram" aria-hidden="true"></i><span class="screen-reader-text">인스타그램</span></a></li>
    			            <li><a href="#"><i class="fa fa-lg fa-youtube" aria-hidden="true"></i><span class="screen-reader-text">유튜브</span></a></li>
    			        </ul>
    			    </div>
    			    <div id="footer-link">
    				    <ul>
    				        <li><a href="#"><b><span class="border-deco">개인정보 처리방침</span></b></a></li>
    				        <li><a href="#"><span class="border-deco">이용약관</span></a></li>
    				        <li><a href="#"><span class="border-deco">회사소개</span></a></li>
    				        <li><a href="#"><span class="border-deco">개인정보취급</span></a></li>
    				        <li><a href="#">Contact Us</a></li>
                        </ul>
    				</div>
    				<?php
    					/**
    					 * Fires before the twentysixteen footer text for footer customization.
    					 *
    					 * @since sir community 1.0
    					 */
    					do_action( 'twentysixteen_credits' );
    				?>   				
    			</div><!-- .site-info -->
			</div>
		</footer><!-- .site-footer -->
	</div><!-- .site-inner -->
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
