$ = jQuery;

$(function(){
    var $body = $('body');
    //$("body").on( "submit",".category-table .category .form-delete", category_delete );
    /*
	$body.on( "click",".category .button-wrapper .add", callback_category_add );
    $body.on( "click",".category .button-wrapper .edit", callback_category_edit );
    $body.on( "click",".category .cancel", callback_category_cancel );
	*/
    $body.on( "change","select.category", callback_change_category );
    $body.on( "change","select.location", callback_change_location );


    $body.on( "click",".filter span.more", callback_collapse_left_sidebar );
    $body.on( "click",".filter-button", callback_filter_options );
    $body.on( "click",".close-filter", callback_filter_options );

    $body.on( "click","div.item-add-submit", callback_submit_add_form );
    $body.on( "submit","form.item-add-form", callback_form_submitted );
	
	init_mall_form_ajax_file_upload('.mall-item-add .addForm-file-upload');
    $body.on( "click",".mall-page .mall-item-add .upload .display-uploaded-files .photo .delete", callback_delete_file );

    $body.on( "change","form.mall-advance-search select", callback_change_sort );
	
	
	
	$("body").on( "keyup",".mall-page .mall-item-add .price-wrapper input[name='price']", callback_check_price );
	
	$("body").on( "keyup",".mall-page .mall-item-add .field-wrapper .important-field", callback_check_empty_field );
	$("body").on( "change",".mall-page .mall-item-add select.important-field", callback_check_empty_field );
	
	
	$("body").on( "click",".mall-view .details .table-wrapper .go-to-view-send-message", callback_move_to_message_form );
	
	
	
	

	
	
	
	/*
	*temporary for market
	*NEED TO MOVE THIS LATER... DON'T FORGET
	*
	*This code has bug though not visible to other users... NEED TO FIX THIS
	*/
	if( $(".mall-view .top-image").length ){
		var total_thumbnails = $(".mall-view .thumbnails .inner img").length;
		var total_pages = Math.ceil( total_thumbnails / 5 );
		var page = 1;
		var is_animating = false;

		$("body").on("click",".mall-view .thumbnails .arrow",function(){
			is_animating = true;
			var $this = $(this);
			if( $this.hasClass('right') ){
				if( page >= total_pages ) return;
				page++;	
				$(".mall-view .thumbnails .inner img").animate({left: "-=100%"}, 500, function(){				
					is_animating = false;
				});
			}
			else if( $this.hasClass('left') ){
				if( page <= 1 ) return;
				page--;
				$selector = $(".mall-view .thumbnails .inner img.is-active");			
				$(".mall-view .thumbnails .inner img").animate({left: "+=100%"}, 500, function(){
					is_animating = false;
				});
			}
		});
		
		/*for market image*/
		var $selector = $(".mall-view .top-image img.is-active");		
		market_top_image_behavior( $selector );
		$selector.load(function(){			
			market_top_image_behavior( $selector );
		});
		
		

		$("body").on("click",".mall-view .thumbnails .inner img",function(){
			var $this = $(this);
			var fid = $this.attr('fid');
			$(".mall-view .top-image img").removeClass('is-active');
			$(".mall-view .thumbnails .inner img.is-active").removeClass('is-active');
			$(".mall-view [fid='" + fid + "']").addClass('is-active');
					
			var $selector = $(".mall-view .top-image img[fid='" + fid + "']");
			market_top_image_behavior( $selector );
		});	
		
		
		var market_window_resize_timeout;
		$(window).resize(function(){
			clearTimeout( market_window_resize_timeout );
			market_window_resize_timeout = setTimeout(function(){
				var $selector = $(".mall-view .top-image img.is-active");		
				market_top_image_behavior( $selector );
			}, 100);
		});				
	}/*eo if( .mall-view .top-image )*/
});

function callback_move_to_message_form(){
	if( $(".view-message-send").length ){
		scroll_top = $(".view-message-send").offset().top - 100;
		$("html,body").animate( { scrollTop:scroll_top },500,function(){
			$(".view-message-send textarea").focus();
		} );	
		//view-message-send
	}
}

var timeout_check_price;

function callback_check_price(){
	clearTimeout( timeout_check_price );
	$selector = $(".mall-page .mall-item-add .price-wrapper");
	timeout_check_price = setTimeout( function(){
		if( $selector.find("input").val() > 0 ){
			if( $selector.find(".note.error").length ){
				$selector.find(".note.error").remove();
				if( $selector.find('input').hasClass('error') ) $selector.find('input').removeClass('error');
			}
			return;
		}
	
		if( $selector.find(".note.error").length ) return;		
		$selector.append( mall_create_form_error_notice("Invalid Price. Must be a positive number and cannot be 0.") );
	}, 300);
}

var timeout_check_empty_field;
function callback_check_empty_field(){	
	clearTimeout( timeout_check_empty_field );
	var $this = $(this);
	//$selector = $(".mall-page .mall-item-add .price-wrapper");
	timeout_check_empty_field = setTimeout( function(){
		checkEmptyField( $this, null );
		/*	
		if( $this.val() == '' || $.trim( $this.val() ).length == 0 ){
			if( $wrapper.find(".note.error").length ) return;
			$wrapper.append( mall_create_form_error_notice( "This field cannot be empty" ) );
		}
		else{
			$wrapper.find(".note.error").remove();
		}*/
	}, 300);
}

function mall_create_form_error_notice( str ){
	return "<div class='note error'>" + str + "</div>";
}


/*for market to be moved later...*/
function market_top_image_behavior( $selector ){	
	var top_image_wrapper_height = $selector.parent().height();
	var top_image_height = $selector.height();
	if( $selector.width() > $(window).width() ){
		$selector.css('width','100%');
	}
	
	if( top_image_height > top_image_wrapper_height ){
		$selector.css('margin-top',0);
		$selector.css('max-height','100%');
	}
	else if( top_image_height < top_image_wrapper_height ){			
		var auto_margin_top = ( top_image_wrapper_height - top_image_height ) / 2;
		$selector.css('margin-top',auto_margin_top);
	}
}
/*for market to be moved later...*/
/*
function callback_category_add(){
	$this = $(this);
	var id = $this.attr("id");
	var form = renderAddForm( id );
	$(".category[category-id='" + id + "']").append( form );
	$(".category[category-id='" + id + "'] input[type='text']").focus();
	$this.remove();	
}

function callback_category_edit(){
	$this = $(this);
	var id = $this.attr("id");
	var form = renderEditForm( id );
	$(".category[category-id='" + id + "'] .label .category-name").html( form );
	$(".category[category-id='" + id + "'] input[type='text']").select();
	$this.remove();
}



function category_delete( e ){
	return confirm( "Are you sure you want to delete - "+e+"?" );
}
*/

function member_delete( e ){
	return confirm( "Are you sure you want to delete member - "+e+"?" );
}

function item_delete( e ){
	return confirm( "Are you sure you want to delete item - "+e+"?" );
}
/*
function callback_category_cancel(){
	 window.location.reload();
}

function renderEditForm( id ){
    var text = '';
	if( $(".category[category-id='" + id + "'] .label .category-name a").length ){
		text = $(".category[category-id='" + id + "'] .label .category-name a").text();
	}
	else{
		text = $(".category[category-id='" + id + "'] .label .category-name").text();
	}
	var markup	=	"<form class='form-update' action='/mall/admin/category/group/update'>" +
					"<fieldset><div class='row'><div class='value'><div class='element'>" +
					"<input type='hidden' name='id' value='" + id + "'>" +
					"<input type='text' name='name' value='" + text + "'><input type='submit' value='Update'>" +
					"</div></div></div></fieldset></form><span class='command cancel'>Cancel</span>";
					
	return markup;
}
*/
function renderAddForm( id ){
	var markup	=	"<form class='form-update' action='/library/category/admin/group/add'>" +
					"<fieldset><div class='row'><div class='value'><div class='element'>" +					
					"<input type='hidden' name='parent_id' value='" + id + "'>" +		
					"<input type='text' name='name' value=''>" +
					"<input type='submit' value='Add'>" +
					"</div></div></div></fieldset></form><span class='command cancel'>Cancel</span>";
					
	return markup;
}
/*
function test_callback( re ){
	console.log( re );
}
*/

function callback_change_category(){
	var $this = $(this);
	var pid = $this.val();
	
	$(".mall-page .mall-item-add select.category").attr("name",'');
	$this.attr("name",'category_id');
	
	var depth = $this.attr('depth');
	
	$this.nextAll('select').remove();
	
	data = {};
	data.call = 'getParentChildren';
	data.pid = pid;
	data.depth = depth;
	
	ajax_api_mall( data, callback_get_parent_children );
}

function callback_change_location(){
	var $this = $(this);
	var province = $this.val();
	var name = $this.attr('name');
	
	if( name == 'province' ){
		$this.nextAll('select').remove();
		
		data = {};
		data.call = 'getCity';
		data.province = province;	
		
		ajax_api_mall( data, callback_get_city );
	}
}

function callback_collapse_left_sidebar(){	
	var $this = $(this);
	var filter = $this.attr('filter');
	
	if( $this.hasClass("less") ){
		$this.removeClass("less");
		text = $this.text();
		text = text.replace("- Less", "+ More");
		$this.html( text );
		$("html,body").scrollTop( $this.parent().parent().offset().top );
	}
	else{
		$this.addClass("less");
		text = $this.text();
		text = text.replace("+ More", "- Less");
		$this.html( text );
	}
	
	$(".filter ." + filter + ".extra").toggle();
}

function callback_filter_options(){
	$(".filter.list").slideToggle();
}

function callback_delete_file(){	
	var $this = $(this);
	var fid = $this.parent().attr('fid');
	
	data = {}
	data.call = 'deleteFile';
	data.fid = fid;
	ajax_api_mall( data, callback_delete_file_result );
}

function callback_submit_add_form(){
	$("form.item-add-form input[type='submit']").click();
}

function callback_form_submitted( e ){	
	checkEmptyField( $(".mall-page .mall-item-add .field-wrapper .important-field[name='title']"), e);
	checkEmptyField( $(".mall-page .mall-item-add .field-wrapper .important-field[name='mobile']"), e);
	checkEmptyField( $(".mall-page .mall-item-add .field-wrapper .category.important-field"), e);
	checkEmptyField( $(".mall-page .mall-item-add .location-wrapper .important-field[name='province']"), e);
	checkEmptyField( $(".mall-page .mall-item-add .location-wrapper .important-field[name='city']"), e);
	
	$price_selector = $(".mall-page .mall-item-add .price-wrapper input[name='price']");
	if( $price_selector.val() < 1 || $price_selector.val() == "" || $.trim( $price_selector.val() ).length == 0 ){
		$wrapper = $(".mall-page .mall-item-add .price-wrapper");
		if( !$wrapper.find(".note.error").length ) {			
			$wrapper.append( mall_create_form_error_notice( "This field cannot be empty or less than 1" ) );
		}
		$(".mall-page .mall-item-add .price-wrapper input[name='price']").addClass("error");
		if( $("html,body").is(':animated') ){
			
		}else{
			scroll_top = $(".mall-page .mall-item-add .price-wrapper input[name='price']").offset().top - 100;
			$("html,body").animate( { scrollTop:scroll_top },500,function(){} );	
			e.preventDefault();
			return false;
		}		
	}
	else{
		if( $wrapper.find(".note.error").length ) $wrapper.find(".note.error").remove();
		$(".mall-page .mall-item-add .price-wrapper input[name='price']").removeClass('error');
	}
	
	total_file_inputs = $("input[type='file']").length;	
	if( $(".upload .display-uploaded-files .photo").length ){
		
	}
	else{
		total_files_uploaded = 0;
		for( var i = 0; i < total_file_inputs; i++ ){		
			if( $("input[type='file']:eq(" + i + ")").val() ){
				total_files_uploaded ++;
			}
		}		
		
		if( total_files_uploaded < 1 ){
			if( !$(".file-upload-group.item_image_thumbnail .note.error").length ){
				$(".file-upload-group.item_image_thumbnail .note:eq(1)").after(  mall_create_form_error_notice( "Atleast one photo upload is required" )  );
			}
		
			if( $("html,body").is(':animated') ){
			
			}else{
				scroll_top = $(".file-upload-group.item_image_thumbnail").offset().top - 100;				
				$("html,body").animate( { scrollTop:scroll_top },500,function(){} );		
				e.preventDefault();
				return false;	
			}			
		}
	}
}

function checkEmptyField( $selector, e ){
	$wrapper = $selector.parent();
	if( $selector.val() == "" || $.trim( $selector.val() ).length == 0 ){
		//alert("Field cannot be empty!");	
		$selector.addClass("error");
		
		if( !$wrapper.find(".note.error").length ) {			
			$wrapper.append( mall_create_form_error_notice( "This field cannot be empty" ) );
		}
				
		if( $("html,body").is(':animated') ){
			
		}else{
			scroll_top = $selector.offset().top - 100;
			$("html,body").animate( { scrollTop:scroll_top },500,function(){} );
		}
		if( e ) e.preventDefault();
		return false;
	}
	else{
		if( $wrapper.find(".note.error").length ) $wrapper.find(".note.error").remove();
		$selector.removeClass('error');
	}
}

/*mall-advance-search*/



/*eo mall-advance-search*/








function ajax_api_mall( qs, callback_function )
{
    var o = {};
    o.data = qs;
    o.url = '/mall/api';
    o.type = "POST";
    var promise = $.ajax( o );
    promise.done( function( o ) {
		trace( o );
        callback_function( o );
    });
    promise.fail( function( re ) {
        alert('ajax call - promise failed');
        trace(re);
    });
}

/*ajax_api_mall callbacks here below*/
function callback_get_parent_children( re ){
	if( re.code == 0 ){
		$('.mall-item-add .categories').append( re.html );
	}
}

function callback_get_city( re ){
	if( re.code == 0 ){
		$('.mall-item-add .location-wrapper .field-wrapper').nextAll().remove();
		$('.mall-item-add .location-wrapper').append( re.html );
		$(".mall-item-add .location-wrapper select[name='city']").addClass( 'important-field' );
	}
}

function callback_delete_file_result( re ){
	console.log(re);
	//,item_image1-42,item_image_thumbnail-43
	if( re.code == 0 ){
		/*removing the fid in input fids*/
		var number = $(".mall-page .mall-item-add .upload .display-uploaded-files .photo[fid='" + re.fid + "']").parents('.upload').attr("no");
		var image_type = $(".mall-page .mall-item-add .upload .display-uploaded-files .photo[fid='" + re.fid + "']").parents('.upload').find("form.addForm-file-upload input[type='file']").attr("name");
		if( typeof( number ) == 'undefined' ) number = '';
		var structure = "," + image_type + number + "-" + re.fid;	
		val = $("input[name='fids']").val();
		val = val.replace( structure, '' );		
		$("input[name='fids']").val( val );
		/*eo removing the fid in input fids*/

		$(".mall-page .mall-item-add .upload .display-uploaded-files .photo[fid='" + re.fid + "']").parents('.upload').find('form.addForm-file-upload').removeClass('is-hidden');
		$(".mall-page .mall-item-add .upload .display-uploaded-files .photo[fid='" + re.fid + "']").remove();
	}
}
/*eo ajax_api_mall callbacks here below*/
function callback_change_sort(){
	var $this = $(this);

	if( !$this.val() ){
		return false;
	}
	else{
		
	}
	
	$this.find("option[selected]").removeAttr("selected");

	$("form.mall-advance-search").submit();
}

/*mall ajax_file_upload*/
function init_mall_form_ajax_file_upload(selector)
{
    var $form = $(selector);	
    if ( $form.length ) {
        hook_mall_file_upload(selector, function(re){			
            if ( re.code == 0 ) {
				//console.log(re);
				var file = re.files;				
				if( file.no ) fid = file.file_usage_type + file.no + "-" + file.fid;
				else fid = file.file_usage_type + "-" + file.fid;
				val = $("input[name='fids']").val();	
				val += ',' + fid;
				$("input[name='fids']").val( val );
				
				html = "<div class='photo' fid='" + file.fid + "'><div class='delete'><span>X</span></div><img src='"+file.url+"'></div>";
				if( file.no ){
					$(".mall-item-add ." + file.file_usage_type + " .upload[no='" + file.no + "'] .display-uploaded-files").append( html );
					$(".mall-item-add ." + file.file_usage_type + " .upload[no='" + file.no + "'] form.addForm-file-upload").addClass('is-hidden');
				}
				else{
					$(".mall-item-add ." + file.file_usage_type + " .upload .display-uploaded-files").append( html );
					$(".mall-item-add ." + file.file_usage_type + " .upload form.addForm-file-upload").addClass('is-hidden');
				}
            }
            else {
                alert( re.error );
            }
        });
    }
}

var $post_progress_array = new Array();

function hook_mall_file_upload(selector, callback)
{	
    var callback_function = callback;
    $('body').on('submit', selector, callback_ajax_file_upload);
    function callback_ajax_file_upload(e) {		
        e.preventDefault();
        ajax_file_upload($(this));
        return false;
    }
    function ajax_file_upload($this)
    {		
        //$(".ajax-file-upload-progress-bar").remove();
        $this.append("<div class='ajax-file-upload-progress-bar'><div class='bar'><div class='percent'></div></div></div>");		
		//console.log("appended to " + $this.attr('class') );				
		
		$post_progress = $this.find(".ajax-file-upload-progress-bar");
		
		$post_progress.show();
        $this.ajaxSubmit({
            beforeSend: function() {
                trace("seforeSend:");
                var percentVal = '0%';
                $post_progress.find('.bar').width('100%');
                $post_progress.find('.percent').width(percentVal);
                $post_progress.find('.percent').html(percentVal);
            },
            uploadProgress: function(event, position, total, percentComplete) {
				
				trace("while uploadProgress:" + percentComplete + '%');
                var percentVal = percentComplete + '%';
				
				if( $(".file-upload-group").length ){
					if( typeof( $post_progress_array[ total ] ) == 'undefined' ){
						$post_progress_array[ total ] = $post_progress;
					}
					
					//$post_progress_array[ total ].find('.bar').width(percentVal);
					/*
					*in case of multiple uploads at the same time this is a temporary solution... 
					*As of now I can't find any other unique per item except for total size ( though it is impossible to be forever unique )
					*/
					$post_progress_array[ total ].find('.percent').width(percentVal);
					$post_progress_array[ total ].find('.percent').html(percentVal);
				}
                else{
					//$post_progress.find('.bar').width(percentVal);
					$post_progress.find('.percent').width(percentVal);
					$post_progress.find('.percent').html(percentVal);
				}
            },
            success: function() {
                trace("upload success:");
                var percentVal = '100%';
                //$post_progress.find('.bar').width(percentVal);
                $post_progress.find('.percent').html(percentVal);
            },
            complete: function(xhr) {
                trace("Upload completed!!");
                var re;
                try {
                    re = JSON.parse(xhr.responseText);
                }
                catch ( e ) {
                    return alert( xhr.responseText );
                }
                trace(re);
                callback_function( re );
				
				//same as temp above
				if( typeof( $post_progress_array[ total ] ) == 'undefined' ) $post_progress.remove();
				else $post_progress_array[ total ].remove();                
            }
        });
    }
}

/*eo mall ajax_file_upload*/
var trace_num = 1;
function trace( re ){
	console.log( re );
	trace_num++;
}