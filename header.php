<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 */
do_action('sir_comm_before_header');
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo get_template_directory_uri(); ?>/css/font-awesome.css" rel="stylesheet" />
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page"><!-- class="site" -->
	<!--<div> class="site-inner" -->
		<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'gnucommerce-2016-summer-ipha' ); ?></a>

		<header id="masthead" class="site-header" role="banner">
		    <div class="site-header-top">
		        <div class="site-inner">
                    <ul class="site-header-top-link">
                        <?php if( is_user_logged_in () ){ ?>
                        <li class="site-link-logout"><a href="<?php echo wp_logout_url(); ?>"><i class="fa fa-sign-in" aria-hidden="true"></i> <?php _e('logout', 'gnucommerce-2016-summer-ipha'); ?></a></li>
                        <li class="site-link-mymember"><a href="<?php echo get_edit_user_link(); ?>"><i class="fa fa-heart-o" aria-hidden="true"></i> <?php _e('mypage', 'gnucommerce-2016-summer-ipha'); ?></a></li>
                        <?php } else { ?>
                        <li class="site-link-login"><a href="<?php echo wp_login_url(); ?>"><i class="fa fa-sign-in" aria-hidden="true"></i> <?php _e('login', 'gnucommerce-2016-summer-ipha'); ?></a></li>
                        <li class="site-link-join"><a href="<?php echo wp_registration_url(); ?>"><i class="fa fa-heart-o" aria-hidden="true"></i> <?php _e('register', 'gnucommerce-2016-summer-ipha'); ?></a></li>
                        <?php } ?>
                    </ul> 
			    </div>
			</div>
			<div class="site-header-main">
				<div class="site-branding">
				    <div class="site-inner">
					<?php sircomm_the_custom_logo(); ?>
					<?php if ( is_front_page() && is_home() ) : ?>
						<h1 class="site-title">
						    <!-- <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"> -->
                            <!-- <?php bloginfo( 'name' ); ?> -->
						      
						    <?php if ( get_header_image() ) : ?>
                            <?php
                                /**
                                 * Filter the default custom header sizes attribute.
                                 *
                                 * @since sir community 1.0
                                 *
                                 * @param string $custom_header_sizes sizes attribute
                                 * for Custom Header. Default '(max-width: 709px) 85vw,
                                 * (max-width: 909px) 81vw, (max-width: 1362px) 88vw, 1200px'.
                                 */
                                $custom_header_sizes = apply_filters( 'sircomm_custom_header_sizes', '(max-width: 709px) 85vw, (max-width: 909px) 81vw, (max-width: 1362px) 88vw, 1200px' );
                            ?>
                            <div class="header-image">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                                    <img src="<?php header_image(); ?>" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( get_custom_header()->attachment_id ) ); ?>" sizes="<?php echo esc_attr( $custom_header_sizes ); ?>" width="<?php echo esc_attr( get_custom_header()->width ); ?>" height="<?php echo esc_attr( get_custom_header()->height ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
                                </a>
                            </div><!-- .header-image -->
                        <?php endif; // End header image check. ?>
                        
						    </a>
						</h1>
					<?php else : ?>
						<!-- <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>  -->

					<?php endif;

					$description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) : ?>
						<!-- <p class="site-description"><?php echo $description; ?></p> -->
					<?php endif; ?>
					</div>
				</div><!-- .site-branding -->
                
                <div class="hd_cate"><!-- site-inner -->
                    <div id="hd_manu">
    				<?php if ( has_nav_menu( 'primary' ) || has_nav_menu( 'social' ) ) : ?>
    					<button id="menu-toggle" class="menu-toggle"><i class="fa fa-bars" aria-hidden="true"></i></button>
    
    					<div id="site-header-menu" class="site-header-menu">
    						<?php if ( has_nav_menu( 'primary' ) ) : ?>
    							<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'gnucommerce-2016-summer-ipha' ); ?>">
    								<?php
    									wp_nav_menu( array(
    										'theme_location' => 'primary',
    										'menu_class'     => 'primary-menu',
    									 ) );
    								?>
    							</nav><!-- .main-navigation -->
    						<?php endif; ?>
    
    						<?php if ( has_nav_menu( 'social' ) ) : ?>
    							<nav id="social-navigation" class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Social Links Menu', 'gnucommerce-2016-summer-ipha' ); ?>">
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
    					</div><!-- .site-header-menu -->
    				<?php endif; ?>

    				</div>

				</div>
			</div><!-- .site-header-main -->
		</header><!-- .site-header -->

		<div id="content" class="site-content site-inner">
