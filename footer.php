<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

		</div><!-- .site-content -->

		<footer id="colophon" class="site-footer" role="contentinfo">
		    <div class="site-inner">
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
    			    <div>
    			        <span class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span>   
    			        <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'twentysixteen' ) ); ?>"><?php printf( __( 'Proudly powered by %s', 'twentysixteen' ), 'WordPress' ); ?></a>
    			    </div>
    			    <div id="name">
    				    <ul>
    				        <li><b>개인정보 처리방침</b></li>
    				        <li>이용약관</li>
    				        <li>회사소개</li>
    				        <li>개인정보취급</li>
    				        <li>Contact Us</li>
                        </ul>
    				</div>
    				<?php
    					/**
    					 * Fires before the twentysixteen footer text for footer customization.
    					 *
    					 * @since Twenty Sixteen 1.0
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
