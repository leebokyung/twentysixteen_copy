<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
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
