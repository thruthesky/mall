$(function(){
    var $body = $('body');
    //$("body").on( "submit",".category-table .category .form-delete", category_delete );
    $("body").on( "click",".category-table .button-wrapper .add", category_add );
    $("body").on( "click",".category-table .button-wrapper .edit", category_edit );
    $("body").on( "click",".category-table .button-wrapper .cancel", category_cancel );
	
	//ajax_api_mall( { call:'test' }, test_callback );
});

function category_add(){
	$this = $(this);
	var id = $this.attr("id");
	var form = renderAddForm( id );
	$(".category[category-id='" + id + "']").append( form );
	$(".category[category-id='" + id + "'] input[type='text']").focus();
	$this.remove();	
}

function category_edit(){
	$this = $(this);
	var id = $this.attr("id");
	var form = renderEditForm( id );
	$(".category[category-id='" + id + "'] .label .category-name").html( form );
	$(".category[category-id='" + id + "'] input[type='text']").select();
	$this.remove();
}



function category_delete( e ){
	return confirm( "Are you sure you want to delete this category?\n Warning that deleting this category will delete ALL of it's sub categories." );
}

function category_cancel(){
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










function ajax_api_mall( qs, callback_function )
{
    var o = {};
    o.data = qs;
    o.url = '/mall/api';
    o.type = "POST";
    var promise = $.ajax( o );
    promise.done( function( o ) {
        callback_function( o );
    });
    promise.fail( function( re ) {
        alert('ajax call - promise failed');
        trace(re);
    });
}