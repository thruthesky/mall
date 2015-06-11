$(function(){
    var $body = $('body');
    //$("body").on( "submit",".category-table .category .form-delete", category_delete );
    $("body").on( "click",".category .button-wrapper .add", callback_category_add );
    $("body").on( "click",".category .button-wrapper .edit", callback_category_edit );
    $("body").on( "click",".category .cancel", callback_category_cancel );
	
	$("body").on( "change","select.category", callback_change_category );
});

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
	if( $(".category[category-id='" + id + "'] .label .category-name a").length ){
		var text = $(".category[category-id='" + id + "'] .label .category-name a").text();
	}
	else{
		var text = $(".category[category-id='" + id + "'] .label .category-name").text();
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

function callback_change_category( re ){
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
	$('.mall-item-add .categories').append( re.html );
}
/*eo ajax_api_mall callbacks here below*/
