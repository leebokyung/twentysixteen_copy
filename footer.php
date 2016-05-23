<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 */
?>

		</div><!-- .site-content -->

		<footer id="colophon" class="site-footer" role="contentinfo">
		    <div class="foot-inner"><!-- site-inner -->
    			<?php if ( has_nav_menu( 'social' ) ) : ?>
    				<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( __('Footer Social Links Menu', 'gnucommerce-2016-summer-ipha') ); ?>">
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
    			        <address class="f-info footer-address">310, Gangnam-daero, Gangnam-gu, Seoul, Korea</address>
    			        <span class="f-info footer-phone">T.02-1234-5678</span>
    			        <span class="f-info footer-fax">F.02-1234-5678</span>
    			        <ul class="footer-sns">
    			            <li><a href="#"><i class="fa fa-lg fa-facebook" aria-hidden="true"></i><span class="screen-reader-text">facebook</span></a></li>
    			            <li><a href="#"><i class="fa fa-lg fa-twitter" aria-hidden="true"></i><span class="screen-reader-text">twitter</span></a></li>
    			            <li><a href="#"><i class="fa fa-lg fa-instagram" aria-hidden="true"></i><span class="screen-reader-text">instagram</span></a></li>
    			            <li><a href="#"><i class="fa fa-lg fa-youtube" aria-hidden="true"></i><span class="screen-reader-text">youtube</span></a></li>
    			        </ul>
    			    </div>
    			    <div id="footer-link">
    				    <ul>
    				        <li><a href="#"><b><span class="border-deco">Privacy Policy</span></b></a></li>
    				        <li><a href="#"><span class="border-deco">Terms of Use</span></a></li>
    				        <li><a href="#"><span class="border-deco">About Us</span></a></li>
    				        <li><a href="#"><span class="border-deco">Privacy</span></a></li>
    				        <li><a href="#">Contact Us</a></li>
                        </ul>
    				</div>
    				<?php
    					/**
    					 *
    					 * @since sir community 1.0
    					 */
    					do_action( 'sircomm_credits' );
    				?>   				
    			</div><!-- .site-info -->
			</div>
		</footer><!-- .site-footer -->
	</div><!-- .site-inner -->
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
