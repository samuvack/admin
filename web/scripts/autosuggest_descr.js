$(function() {
	$( "#form_description" ).autocomplete({
	  source: "scripts/autocomplete_descr.php",
	  minLength: 2,
	  select: function( event, ui ) {
			$("#form_description").val(ui.item.item);
			$("form:first-of-type").submit();
	  }
	});
});