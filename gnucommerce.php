<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
        
        <article class="gnucommerce type-gnucommerce" >
            <?php
            do_action('gc_before_content_print');

            gc_load_content();

            do_action('gc_after_content_print');
            ?>
        
        </article>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>