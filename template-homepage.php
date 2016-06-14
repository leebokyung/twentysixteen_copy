<?php
/**
 * The template for displaying the homepage.
 *
 * Template name: Homepage
 *
 * @package sir-furniture
 */

get_header(); ?>

	<div id="primary" class="content-area">
	    
        <?php
        // admin/main/options/homepage.php sircomm_input_homepagesection

        do_action('sir_community_main_area');

        ?>

	    <div id="idx-new-content">
            
            <?php do_action('sir_community_main_latest'); ?>
            <?php do_action('sir_community_main_content'); ?>
            
    	</div> <?php // end html idx-new-content ?>
	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
