(function($){

    $.when( gc_tag_ajax() ).then(function( data, textStatus, jqXHR ) {
        
        gcboard.wr_tags = data;

        $("#wr_tag_input").hide();
        $('#gc_singleFieldTags').tagit({
            availableTags: gcboard.wr_tags,
            placeholderText : gc_object.placeholderText,
            autocomplete: {delay: 0, minLength: 1},
            singleField: true,
            singleFieldNode: $('#wr_tag_input'),
            allowSpaces : true,
            onTagLimitExceeded : function(e, ui){}
        }).on("keypress keydown keyup change", "input", function(e){
            if(e.keyCode == 13) { // Enter 방지
                e.preventDefault();
                return false;
            }
        });
    });

    function gc_tag_ajax() {
        // NOTE:  This function must return the value 
        //        from calling the $.ajax() method.

        var bo_table = $("input[name='bo_table']").val(),
			security = $("input[name='gc_nonce_field']").val(); //nonce체크

        return $.ajax({
            url: gcboard.ajax_url,
            dataType: "json",
            data: { action : 'gc_get_tags', bo_table : bo_table, security : security }
        });
    }

})(jQuery);