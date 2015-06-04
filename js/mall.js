$(function(){	
	$("body").on("submit",".category .form-delete",function(){
		re = confirm( "Are you sure you want to delete this category?\n Warning that deleting this category will delete ALL of it's sub categories." );
		if( re ){
		}
		else{
			return false;
		}
	});
	
	$("body").on("click",".category .edit",function(){		
		$this = $(this);
		var root_id = $this.attr("root_id");
		var id = $this.attr("id");
		
		var form = renderEditForm( root_id, id );
		console.log( $(".category[category-id='" + id + "'] .label .category-name").length );
		$(".category[category-id='" + id + "'] .label .category-name").html( form );
	});
});

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