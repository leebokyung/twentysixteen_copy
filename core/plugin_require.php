<?php
if( ! defined( 'ABSPATH' ) ) exit;

if (!function_exists('summeripha_setup')) {

	function summeripha_setup() {

        global $summeripha_global;
        $summeripha_global = array();

		add_theme_support( 'gnucommerce' );
		
		require_once( trailingslashit( get_template_directory() ) . 'core/tgm/class-tgm-plugin-activation.php' );
		require_once( trailingslashit( get_template_directory() ) . 'core/classes/customize.php' );
        require_once( trailingslashit( get_template_directory() ) . 'core/admin/customize/customize.php' );
        
        require_once( trailingslashit( get_template_directory() ) . 'core/widget/gnucommerce_widget.php' );
        require_once( trailingslashit( get_template_directory() ) . 'core/widget/short_login_widget.php' );

        require_once( trailingslashit( get_template_directory() ) . 'core/widget/widget_functions.php' );
    
        require_once( trailingslashit( get_template_directory() ) . 'core/template-functions.php' );
	}

	add_action( 'after_setup_theme', 'summeripha_setup', 12 );


    /* widget_action */
    add_action( 'sir_community_main_area', 'sircomm_input_sliderhome', 12);
    add_action( 'sir_community_main_area', 'sircomm_input_homepagesection', 13);

}

// Add styles
add_action( 'wp_enqueue_scripts', 'summeripha_add_enqueue_styles' );
function summeripha_add_enqueue_styles() {

    wp_enqueue_style( 'sir-comm-add-style',
        get_template_directory_uri() . '/css/add.css'
    );
    
    // Add script
    wp_enqueue_script( 'sir_comm_mainjs', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), 'true' );
}

if (!function_exists('summeripha_get_option')) {
    function summeripha_get_option($id, $default=''){
		$summeripha_option = get_theme_mod($id);
			
		if( $summeripha_option ){
            return $summeripha_option;
        }
		
        return $default;
    }
}

if ( !class_exists( 'SR_register_required_plugins' ) ) :

Class SR_register_required_plugins {
    public function __construct() {
        add_action( 'tgmpa_register', array( $this, 'required_plugins') );
   
        add_action( 'sir_community_main_area', array( $this, 'sir_community_main_area_widget' ), 13 );
        add_action( 'sir_community_main_content',	array( $this, 'sir_community_main_latest_widget' ) );
        add_action( 'sir_community_main_content',	array( $this, 'sir_community_main_gallery_widget' ) );
    }

    public function sir_community_main_area_widget(){
        if ( is_active_sidebar( 'main-head-latest' ) ) {
            ?>
		    <div class="sir-comm-main_area_widget" role="complementary">
				<?php dynamic_sidebar( 'main-head-latest' ); ?>
		    </div>
            <?php
        }
    }

    public function sir_community_main_latest_widget(){

        if ( is_active_sidebar( 'main-latest-50pro' ) ) {
            ?>
		    <div class="sir-comm-main-latest-50pro-widget" role="complementary">
				<?php dynamic_sidebar( 'main-latest-50pro' ); ?>
		    </div>
            <?php
        }

    }

    public function sir_community_main_gallery_widget(){
        if ( is_active_sidebar( 'main-gallery-latest' ) ) {
            ?>
		    <div class="main-gallery-latest-widget" role="complementary">
				<?php dynamic_sidebar( 'main-gallery-latest' ); ?>
		    </div>
            <?php
        }
    }

    public function sir_community_main_text_widget(){

		if ( is_active_sidebar( 'main-latest-4' ) ) {
			$widget_columns = apply_filters( 'sir_community_main_widget_regions', 4 );
		} elseif ( is_active_sidebar( 'main-latest-3' ) ) {
			$widget_columns = apply_filters( 'sir_community_main_widget_regions', 3 );
		} elseif ( is_active_sidebar( 'main-latest-2' ) ) {
			$widget_columns = apply_filters( 'sir_community_main_widget_regions', 2 );
		} elseif ( is_active_sidebar( 'main-latest-1' ) ) {
			$widget_columns = apply_filters( 'sir_community_main_widget_regions', 1 );
		} else {
			$widget_columns = apply_filters( 'sir_community_main_widget_regions', 0 );
		}

        $k = 0;

		if ( $widget_columns > 0) {
            for ( $i = 1; $i <= intval( $widget_columns ); $i++ ) {
                if ( ! is_active_sidebar('main-latest-' . $i) ) continue;
                
                $add_class = 'new-content';

                if( ($k%2) == 1 ){
                    $add_class .= ' new-content-nomargin';
                }
		?>
		    <div class="main-latest-widget-<?php echo $i;?> <?php echo $add_class; ?>" role="complementary">
				<?php dynamic_sidebar( 'main-latest-'. $i ); ?>
		    </div>
		<?php
            $k++;

            }   //end for
		}   //end if
    }

    public function required_plugins(){

		$plugins = array(

			array(
				'name'      => 'GNUCommerce',
				'slug'      => 'gnucommerce',
				'required'  => false,
			),
	
		);

		$config = array(
			'menu'         => 'summeripha-install-plugins', 
			'parent_slug'  => 'themes.php',
		);

		tgmpa( $plugins, $config );

    }
}

new SR_register_required_plugins();

endif;  //Class exists SR_register_required_plugins end if
?>