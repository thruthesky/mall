$(function(){
	$("body").on( "submit",".category .form-delete", category_delete );	
	$("body").on( "click",".category .edit", category_edit );
	
	//ajax_api_mall( { call:'test' }, test_callback );
});

function category_edit(){
	$this = $(this);
	var root_id = $this.attr("root_id");
	var id = $this.attr("id");
	if( $(".category[category-id='" + id + "'] .label .category-name .form-update").length ){
		console.log( "exists" );
		return;
	}
	var form = renderEditForm( root_id, id );
	$(".category[category-id='" + id + "'] .label .category-name").html( form );
}

function category_delete(){
	re = confirm( "Are you sure you want to delete this category?\n Warning that deleting this category will delete ALL of it's sub categories." );
	if( !re ) return false;
}

function renderEditForm( root_id, id ){
	if( $(".category[category-id='" + id + "'] .label .category-name a").length ){
		var text = $(".category[category-id='" + id + "'] .label .category-name a").text();
	}
	else{
		var text = $(".category[category-id='" + id + "'] .label .category-name").text();
	}
	var markup	=	"<form class='form-update' action='/mall/admin/category/group/update'>" +
					"<fieldset><div class='row'><div class='value'><div class='element'>" +
					"<input type='hidden' name='root_id' value='" + root_id + "'>" +
					"<input type='hidden' name='id' value='" + id + "'>" +
					"<input type='text' name='name' value='" + text + "'><input type='submit' value='Update'>" +
					"</div></div></div></fieldset></form>";
					
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