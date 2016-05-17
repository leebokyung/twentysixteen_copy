(function( $ ) {
	
	$(document).ready(function (){

		// Only needed on customizer page - theme options page handled using Redux core customization
		if( jQuery( 'body' ).hasClass( 'wp-customizer' ) ) {

			// ----------------------------------------------------------------------------------------------------------
			// 1. CUSTOMIZER PAGE
			// ----------------------------------------------------------------------------------------------------------

			// Add active class to customizer
			$('body.wp-customizer #accordion-panel-sircomm_theme_options > h3').click(function(e){ 

				var target_control = '#customize-controls';

				// Remove width classes
				$( target_control ).removeClass( 'sircomm-width-450' );

				// Remove width classes
				$( target_control ).addClass( 'sircomm-width-450' );
			});


			// Remove width classed WordPress v4.3+
			$( 'body.wp-customizer #accordion-panel-sircomm_theme_options > ul > li > .customize-panel-back' || '.control-panel-back' ).click(function(e){ 

				var target_control = '#customize-controls';

				$( target_control ).removeClass( 'sircomm-width-450' );
			});

			// Remove width classed WordPress pre v4.3
			$( 'body.wp-customizer #customize-header-actions > .primary-actions > .control-panel-back' ).click(function(e){ 

				var target_control = '#customize-controls';

				$( target_control ).removeClass( 'sircomm-width-450' );

			});
		}

	});


	// ----------------------------------------------------------------------------------
	//	2.1. HIDE / SHOW OPTION WHEN CHANGED BY USER - CUSTOMIZER
	// ----------------------------------------------------------------------------------
	jQuery(document).ready(function(){

		// Only needed on customizer page - theme options page handled using Redux core customization
		if( jQuery( 'body' ).hasClass( 'wp-customizer' ) ) {

			jQuery('input[type=radio]').change(function() {

				// General - Logo Settings (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_general_logoswitch input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_general_logolink').addClass('sircomm-show').removeClass('sircomm-hide');
					jQuery('#sir_comm_redux_variables-sircomm_general_logolinkretina').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_general_logoswitch input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_general_logolink').addClass('sircomm-hide').removeClass('sircomm-show');
					jQuery('#sir_comm_redux_variables-sircomm_general_logolinkretina').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// General - Logo Settings (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_general_logoswitch input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_general_sitetitle').addClass('sircomm-show').removeClass('sircomm-hide');
					jQuery('#sir_comm_redux_variables-sircomm_general_sitedescription').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_general_logoswitch input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_general_sitetitle').addClass('sircomm-hide').removeClass('sircomm-show');
					jQuery('#sir_comm_redux_variables-sircomm_general_sitedescription').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Enable Slider
				if(jQuery('#sircomm_homepage_sliderswitch-buttonsetoption1').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpreset').addClass('sircomm-show').removeClass('sircomm-hide');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderspeed').addClass('sircomm-show').removeClass('sircomm-hide');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderstyle').addClass('sircomm-show').removeClass('sircomm-hide');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpresetwidth').addClass('sircomm-show').removeClass('sircomm-hide');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpresetheight').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sircomm_homepage_sliderswitch-buttonsetoption1').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpreset').addClass('sircomm-hide').removeClass('sircomm-show');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderspeed').addClass('sircomm-hide').removeClass('sircomm-show');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderstyle').addClass('sircomm-hide').removeClass('sircomm-show');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpresetwidth').addClass('sircomm-hide').removeClass('sircomm-show');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpresetheight').addClass('sircomm-hide').removeClass('sircomm-show');
				}
				if(jQuery('#sircomm_homepage_sliderswitch-buttonsetoption2').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_slidername').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sircomm_homepage_sliderswitch-buttonsetoption2').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_slidername').addClass('sircomm-hide').removeClass('sircomm-show');
				}
				if(jQuery('#sircomm_homepage_sliderswitch-buttonsetoption4').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpage1').addClass('sircomm-show').removeClass('sircomm-hide');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpage2').addClass('sircomm-show').removeClass('sircomm-hide');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpage3').addClass('sircomm-show').removeClass('sircomm-hide');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpresetwidth').addClass('sircomm-show').removeClass('sircomm-hide');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpresetheight').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sircomm_homepage_sliderswitch-buttonsetoption4').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpage1').addClass('sircomm-hide').removeClass('sircomm-show');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpage2').addClass('sircomm-hide').removeClass('sircomm-show');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpage3').addClass('sircomm-hide').removeClass('sircomm-show');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpresetwidth').addClass('sircomm-hide').removeClass('sircomm-show');
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sliderpresetheight').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Call To Action Intro Link (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionpage').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionpage').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Call To Action Intro Link (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactioncustom').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactioncustom').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Button 1 Call To Action Intro Link (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink1 input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionpage1').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink1 input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionpage1').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Button 1 Call To Action Intro Link (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink1 input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactioncustom1').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink1 input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactioncustom1').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Button 2 Call To Action Intro Link (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink2 input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionpage2').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink2 input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionpage2').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Button 2 Call To Action Intro Link (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink2 input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactioncustom2').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_introactionlink2 input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_introactioncustom2').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Call To Action Outro Link (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionpage').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionpage').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Call To Action Outro Link (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactioncustom').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactioncustom').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Button 1 Call To Action Outro Link (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink1 input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionpage1').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink1 input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionpage1').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Button 1 Call To Action Outro Link (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink1 input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactioncustom1').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink1 input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactioncustom1').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Button 2 Call To Action Outro Link (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink2 input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionpage2').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink2 input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionpage2').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Homepage - Button 2 Call To Action Outro Link (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink2 input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactioncustom2').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactionlink2 input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_outroactioncustom2').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Header - Choose Header Style (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_header_styleswitch input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_header_locationswitch').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_header_styleswitch input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_header_locationswitch').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Footer - Call To Action Outro Link (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionpage').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionpage').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Footer - Call To Action Outro Link (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactioncustom').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactioncustom').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Footer - Button 1 Call To Action Outro Link (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink1 input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionpage1').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink1 input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionpage1').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Footer - Button 1 Call To Action Outro Link (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink1 input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactioncustom1').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink1 input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactioncustom1').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Footer - Button 2 Call To Action Outro Link (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink2 input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionpage2').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink2 input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionpage2').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Footer - Button 2 Call To Action Outro Link (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink2 input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactioncustom2').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_footer_outroactionlink2 input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_footer_outroactioncustom2').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Notification Bar - Add Button Link (Option 1)
				if(jQuery('#sir_comm_redux_variables-sircomm_notification_link input[value=option1]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_notification_linkpage').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_notification_link input[value=option1]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_notification_linkpage').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Notification Bar - Add Button Link (Option 2)
				if(jQuery('#sir_comm_redux_variables-sircomm_notification_link input[value=option2]').is(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_notification_linkcustom').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_notification_link input[value=option2]').not(":checked")){
					jQuery('#sir_comm_redux_variables-sircomm_notification_linkcustom').addClass('sircomm-hide').removeClass('sircomm-show');
				}
			});
		}
	});

	// ----------------------------------------------------------------------------------
	//	2.2. HIDE / SHOW OPTIONS ON SIDEBAR IMAGE CLICK - CUSTOMIZER
	// ----------------------------------------------------------------------------------
	jQuery(document).ready(function(){

		// Only needed on customizer page - theme options page handled using Redux core customization
		if( jQuery( 'body' ).hasClass( 'wp-customizer' ) ) {

			jQuery('input[type=radio]').change(function() {

				// Select sidebar for Page Layout
				if( jQuery('#sir_comm_redux_variables-sircomm_general_layout input[value=option2]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_general_layout input[value=option3]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_general_sidebars').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_general_layout input[value=option2]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_general_layout input[value=option3]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_general_sidebars').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Select sidebar for Homepage Layout
				if( jQuery('#sir_comm_redux_variables-sircomm_homepage_layout input[value=option2]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_homepage_layout input[value=option3]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sidebars').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_homepage_layout input[value=option2]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_homepage_layout input[value=option3]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_homepage_sidebars').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Select sidebar for Blog Layout
				if( jQuery('#sir_comm_redux_variables-sircomm_blog_layout input[value=option2]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_blog_layout input[value=option3]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_blog_sidebars').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_blog_layout input[value=option2]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_blog_layout input[value=option3]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_blog_sidebars').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Select Blog Style - DONE
				if( jQuery('#sir_comm_redux_variables-sircomm_blog_style input[value=option1]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_blog_style1layout').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_blog_style input[value=option1]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_blog_style1layout').addClass('sircomm-hide').removeClass('sircomm-show');
				}
				if( jQuery('#sir_comm_redux_variables-sircomm_blog_style input[value=option2]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_blog_style2layout').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_blog_style input[value=option2]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_blog_style2layout').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Select Blog Style - DONE
				if( jQuery('#sir_comm_redux_variables-sircomm_blog_postswitch input[value=option1]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_blog_postexcerpt').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_blog_postswitch input[value=option1]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_blog_postexcerpt').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Select sidebar for Post Layout
				if( jQuery('#sir_comm_redux_variables-sircomm_post_layout input[value=option2]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_post_layout input[value=option3]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_post_sidebars').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_post_layout input[value=option2]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_post_layout input[value=option3]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_post_sidebars').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Select sidebar for Portfolio Layout
				if( jQuery('#sir_comm_redux_variables-sircomm_portfolio_layout input[value=option5]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_portfolio_layout input[value=option6]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_portfolio_layout input[value=option7]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_portfolio_layout input[value=option8]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_portfolio_sidebars').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_portfolio_layout input[value=option5]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_portfolio_layout input[value=option6]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_portfolio_layout input[value=option7]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_portfolio_layout input[value=option8]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_portfolio_sidebars').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Select sidebar for Project Layout
				if( jQuery('#sir_comm_redux_variables-sircomm_project_layout input[value=option2]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_project_layout input[value=option3]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_project_sidebars').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_project_layout input[value=option2]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_project_layout input[value=option3]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_project_sidebars').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Select sidebar for WooCommerce Shop Layout
				if( jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layout input[value=option5]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layout input[value=option6]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layout input[value=option7]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layout input[value=option8]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_woocommerce_sidebars').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layout input[value=option5]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layout input[value=option6]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layout input[value=option7]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layout input[value=option8]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_woocommerce_sidebars').addClass('sircomm-hide').removeClass('sircomm-show');
				}

				// Select sidebar for WooCommerce Product Layout  - DONE
				if( jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layoutproduct input[value=option2]').is(":checked") || jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layoutproduct input[value=option3]').is(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_woocommerce_sidebarsproduct').addClass('sircomm-show').removeClass('sircomm-hide');
				}
				else if(jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layoutproduct input[value=option2]').not(":checked") || jQuery('#sir_comm_redux_variables-sircomm_woocommerce_layoutproduct input[value=option3]').not(":checked") ){
					jQuery('#sir_comm_redux_variables-sircomm_woocommerce_sidebarsproduct').addClass('sircomm-hide').removeClass('sircomm-show');
				}
			});
		}
	});

})( jQuery );
