( function( wp, $ ) {

    var first_init = 0;

    var button_change = function( val ){

        var show = "show",
            hide = "hide",
            $el1 = $("#customize-control-sircomm_homepage_slidername"),
            $el2 = $("#customize-control-sircomm_homepage_sliderpage1"),
            $el3 = $("#customize-control-sircomm_homepage_sliderpage2"),
            $el4 = $("#customize-control-sircomm_homepage_sliderpage3"),
            $icon_sets = $("[id^=customize-control-sircomm_homepage_section]");

        if( val == 'option3' ){
            $el1.hide();
            $el2.hide();
            $el3.hide();
            $el4.hide();
            /*
            $el1.removeClass(show).addClass(hide);
            $el2.removeClass(show).addClass(hide);
            $el3.removeClass(show).addClass(hide);
            $el4.removeClass(show).addClass(hide);
            */
        } else if( val == 'option2' ){
            $el1.show();
            $el2.hide();
            $el3.hide();
            $el4.hide();
            /*
            $el1.removeClass(hide).addClass(show);
            $el2.removeClass(show).addClass(hide);
            $el3.removeClass(show).addClass(hide);
            $el4.removeClass(show).addClass(hide);
            */
        } else if( val == 'option4' ){
            $el1.hide();
            $el2.show();
            $el3.show();
            $el4.show();
            /*
            $el1.removeClass(show).addClass(hide);
            $el2.removeClass(hide).addClass(show);
            $el3.removeClass(hide).addClass(show);
            $el4.removeClass(hide).addClass(show);
            */
        } else if( val == 'switch_on' ){
            $icon_sets.show();
        } else if( val == 'switch_off' ){
            $icon_sets.hide();
        }
    }

    wp.customize.button_Control = wp.customize.Control.extend({
        ready: function() {
            var control = this,
                el = $(".customize-control-summeripha_button_set");

            el.find( '.buttonset' ).each(
                function() {
                    if ( $( this ).is( ':checkbox' ) ) {
                        $( this ).find( '.buttonset-item' ).button();
                    }

                    $( this ).buttonset();
                }
            );

            this.container.on( 'change', '.buttonset-item',
                function() {
                    var value = $(this).val();

                    control.setting.set( value );
                    button_change( value );
                }
            );

            if( ! first_init ){
                setTimeout(function(){
                    
                    var input1 = $("input[name$='sircomm_homepage_sliderswitch']:checked").val(),
                        input2 = $("input[name$='sircomm_homepage_icon_switch']:checked").val();

                    button_change( input1 );
                    button_change( input2 );

                }, 1000);

                first_init = 1;
            }
        }
	});

	$.extend( wp.customize.controlConstructor, {
		'summeripha_button_set': wp.customize.button_Control,
	} );

} )( wp, jQuery );