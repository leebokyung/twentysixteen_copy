<?php
/**
 * Template for displaying search forms in sir community
 *
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'gnucommerce-2016-summer-ipha' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr( __('Search &hellip;', 'gnucommerce-2016-summer-ipha') ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	</label>
	<button type="submit" class="search-submit"><span class="screen-reader-text"><?php echo _x( 'Search', 'submit button', 'gnucommerce-2016-summer-ipha' ); ?></span></button>
</form>
