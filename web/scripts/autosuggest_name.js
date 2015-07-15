$(function() {
	$( "input:first-of-type" ).autocomplete({
	  source: "scripts/autocomplete_name.php",
	  minLength: 2,
	  select: function( event, ui ) {
		  $("#form_name").val(ui.item.id);
		  $("form:first-of-type").submit();
	  }
	});
});