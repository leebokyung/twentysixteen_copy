<?php
if( ! defined( 'ABSPATH' ) ) exit;

//https://hasin.me/2015/04/24/getting-rid-of-redux-framework-annoyances/
//https://codex.wordpress.org/Theme_Frameworks

require_once( get_template_directory() . '/lib/define.php' );

// Add Redux Framework
require_once( get_template_directory() . '/admin/main/framework.php' );
require_once( get_template_directory() . '/admin/main/lib.php' );
require_once( get_template_directory() . '/admin/main/options.php' );

// Add custom theme options ( related Redux Framework )
require_once( get_template_directory() . '/admin/main/options/variables.php' );
require_once( get_template_directory() . '/admin/main/options/homepage.php' );

// Add widget
require_once( get_template_directory() . '/lib/widget_functions.php' );

// Add login widget
require_once( get_template_directory() . '/classes/short_login_widget.php' );

// Add styles
add_action( 'wp_enqueue_scripts', 'sir_comm_add_enqueue_styles' );
function sir_comm_add_enqueue_styles() {

    wp_enqueue_style( 'sir-comm-add-style',
        get_stylesheet_directory_uri() . '/css/add.css'
    );
    
    // Add script
    wp_enqueue_script( 'sir_comm_mainjs', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), 'true' );
}


// 메인 슬라이더 표시
add_action( 'sir_community_main_area', 'sircomm_input_sliderhome', 12);

// 메인 아이콘 표시
add_action( 'sir_community_main_area', 'sircomm_input_homepagesection', 13);

/*
 function sir_comm_removeDemoModeLink() {   // Be sure to rename this function to something more unique

       if   (   class_exists  (  'Redux'  ) ) { 
           remove_filter(   'plugin_row_meta'  ,   array  ( ReduxFramework::get_instance(),   'plugin_metalinks'  ), null, 2 ); 
       } 
       if   (   class_exists  (  'Redux'  ) ) {
           remove_action(  'admin_notices'  ,   array  ( ReduxFramework::get_instance(),   'admin_notices'   ) );     
       } 

 } 
 add_action(  'init'  ,   'sir_comm_removeDemoModeLink'  ); 
*/

/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.5.2 for parent theme Sir Community
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */

require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

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
                'name'     				=> 'GNUCommerce', // The plugin name
                'slug'     				=> 'gnucommerce', // The plugin slug (typically the folder name)
                'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
                'version' 				=> '0.5',
                'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
            )
        );


        /*
         * Array of configuration settings. Amend each line as needed.
         *
         * TGMPA will start providing localized text strings soon. If you already have translations of our standard
         * strings available, please help us make TGMPA even better by giving us access to these translations or by
         * sending in a pull-request with .po file(s) with the translations.
         *
         * Only uncomment the strings in the config array if you want to customize the strings.
         */
        $config = array(
            'id'           => 'sir_community',                 // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '',                      // Default absolute path to bundled plugins.
            'menu'         => 'tgmpa-install-plugins', // Menu slug.
            'parent_slug'  => 'themes.php',            // Parent menu slug.
            'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
            'has_notices'  => true,                    // Show admin notices or not.
            'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => false,                   // Automatically activate plugins after installation or not.
            'message'      => '',                      // Message to output right before the plugins table.

            /*
            'strings'      => array(
                'page_title'                      => __( 'Install Required Plugins', 'sir_community' ),
                'menu_title'                      => __( 'Install Plugins', 'sir_community' ),
                'installing'                      => __( 'Installing Plugin: %s', 'sir_community' ), // %s = plugin name.
                'oops'                            => __( 'Something went wrong with the plugin API.', 'sir_community' ),
                'notice_can_install_required'     => _n_noop(
                    'This theme requires the following plugin: %1$s.',
                    'This theme requires the following plugins: %1$s.',
                    'sir_community'
                ), // %1$s = plugin name(s).
                'notice_can_install_recommended'  => _n_noop(
                    'This theme recommends the following plugin: %1$s.',
                    'This theme recommends the following plugins: %1$s.',
                    'sir_community'
                ), // %1$s = plugin name(s).
                'notice_cannot_install'           => _n_noop(
                    'Sorry, but you do not have the correct permissions to install the %1$s plugin.',
                    'Sorry, but you do not have the correct permissions to install the %1$s plugins.',
                    'sir_community'
                ), // %1$s = plugin name(s).
                'notice_ask_to_update'            => _n_noop(
                    'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
                    'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
                    'sir_community'
                ), // %1$s = plugin name(s).
                'notice_ask_to_update_maybe'      => _n_noop(
                    'There is an update available for: %1$s.',
                    'There are updates available for the following plugins: %1$s.',
                    'sir_community'
                ), // %1$s = plugin name(s).
                'notice_cannot_update'            => _n_noop(
                    'Sorry, but you do not have the correct permissions to update the %1$s plugin.',
                    'Sorry, but you do not have the correct permissions to update the %1$s plugins.',
                    'sir_community'
                ), // %1$s = plugin name(s).
                'notice_can_activate_required'    => _n_noop(
                    'The following required plugin is currently inactive: %1$s.',
                    'The following required plugins are currently inactive: %1$s.',
                    'sir_community'
                ), // %1$s = plugin name(s).
                'notice_can_activate_recommended' => _n_noop(
                    'The following recommended plugin is currently inactive: %1$s.',
                    'The following recommended plugins are currently inactive: %1$s.',
                    'sir_community'
                ), // %1$s = plugin name(s).
                'notice_cannot_activate'          => _n_noop(
                    'Sorry, but you do not have the correct permissions to activate the %1$s plugin.',
                    'Sorry, but you do not have the correct permissions to activate the %1$s plugins.',
                    'sir_community'
                ), // %1$s = plugin name(s).
                'install_link'                    => _n_noop(
                    'Begin installing plugin',
                    'Begin installing plugins',
                    'sir_community'
                ),
                'update_link' 					  => _n_noop(
                    'Begin updating plugin',
                    'Begin updating plugins',
                    'sir_community'
                ),
                'activate_link'                   => _n_noop(
                    'Begin activating plugin',
                    'Begin activating plugins',
                    'sir_community'
                ),
                'return'                          => __( 'Return to Required Plugins Installer', 'sir_community' ),
                'plugin_activated'                => __( 'Plugin activated successfully.', 'sir_community' ),
                'activated_successfully'          => __( 'The following plugin was activated successfully:', 'sir_community' ),
                'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'sir_community' ),  // %1$s = plugin name(s).
                'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'sir_community' ),  // %1$s = plugin name(s).
                'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'sir_community' ), // %s = dashboard link.
                'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'sir_community' ),

                'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
            ),
            */
        );

        tgmpa( $plugins, $config );
    }
}

new SR_register_required_plugins();

endif;  //Class exists SR_register_required_plugins end if
?>