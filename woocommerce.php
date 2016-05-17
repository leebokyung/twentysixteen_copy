<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
        
        <article class="woocommerce" >
            <?php woocommerce_content(); ?>
        
        </article>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>