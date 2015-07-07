$ = jQuery;

$(function(){
    var $body = $('body');
    //$("body").on( "submit",".category-table .category .form-delete", category_delete );
    $body.on( "click",".category .button-wrapper .add", callback_category_add );
    $body.on( "click",".category .button-wrapper .edit", callback_category_edit );
    $body.on( "click",".category .cancel", callback_category_cancel );

    $body.on( "change","select.category", callback_change_category );
    $body.on( "change","select.location", callback_change_location );


    $body.on( "click",".filter span.more", callback_collapse_left_sidebar );
    $body.on( "click",".filter-button", callback_filter_options );
    $body.on( "click",".close-filter", callback_filter_options );

    $body.on( "click","div.item-add-submit", callback_submit_add_form );
	init_mall_form_ajax_file_upload('.mall-item-add .addForm-file-upload');
    $body.on( "click",".mall-page .mall-item-add .upload .display-uploaded-files .photo .delete", callback_delete_file );

    $body.on( "change","form.mall-advance-search select", callback_change_sort );
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	*temporary for market
	*NEED TO MOVE THIS LATER... DON'T FORGET
	*
	*This code has bug though not visible to other users... NEED TO FIX THIS
	*/
	if( $(".mall-view .top-image").length ){
		var total_thumbnails = $(".mall-view .thumbnails .inner img").length;
		var total_pages = Math.ceil( total_thumbnails / 6 );
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

function member_delete( e ){
	return confirm( "Are you sure you want to delete member - "+e+"?" );
}

function item_delete( e ){
	return confirm( "Are you sure you want to delete item - "+e+"?" );
}

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

function renderAddForm( id ){
	var markup	=	"<form class='form-update' action='/mall/admin/category/group/add'>" +
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
	$("form.item-add-form").submit();
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
		$('.mall-item-add .location-wrapper').append( re.html );
	}
}

function callback_delete_file_result( re ){
	if( re.code == 0 ){		
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
            if ( re.code ) {
				trace( re );
            }
            else if ( re.files ) {
                console.log(re);
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
        });
    }
}

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
        $(".ajax-file-upload-progress-bar").remove();
        $this.append("<div class='ajax-file-upload-progress-bar'><div class='bar'><div class='percent'></div></div></div>");
        $post_progress = $(".ajax-file-upload-progress-bar");
        $this.ajaxSubmit({
            beforeSend: function() {
                trace("seforeSend:");
                var percentVal = '0%';
                $post_progress.find('.bar').width(percentVal);
                $post_progress.find('.percent').html(percentVal);
            },
            uploadProgress: function(event, position, total, percentComplete) {
                trace("while uploadProgress:" + percentComplete + '%');
                var percentVal = percentComplete + '%';
                $post_progress.find('.bar').width(percentVal);
                $post_progress.find('.percent').html(percentVal);
            },
            success: function() {
                trace("upload success:");
                var percentVal = '100%';
                $post_progress.find('.bar').width(percentVal);
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
                $post_progress.remove();
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