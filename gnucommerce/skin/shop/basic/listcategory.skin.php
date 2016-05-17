<?php
if( !defined('GC_NAME') ) exit;

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );

$cats = $categories['category_info'];

if ( $cats ) {

	echo $categories['wrap_before'];

	foreach ( $cats as $key => $crumb ) {

		if ( ! empty( $crumb[1] ) && sizeof( $cats ) !== $key + 1 ) {
			echo '<a href="' . esc_url( $crumb[1] ) . '" class="sct_bg">' . esc_html( $crumb[0] ) . '</a>';
		} else {
			echo '<span class="sct_here">'.esc_html( $crumb[0] ).'</span>';
		}

		if ( sizeof( $cats ) !== $key + 1 ) {
			echo $categories['delimiter'];
		}

	}

	echo $categories['wrap_after'];

}

?>

<?php do_action('gc_category_view_after'); ?>