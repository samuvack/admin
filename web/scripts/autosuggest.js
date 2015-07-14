$(function() {
	/*function log( message ) {
	  $( "<div>" ).text( message ).prependTo( "#results" );
	  //$( "#results" ).scrollTop( 0 );
	}*/

	$( "#form_search" ).autocomplete({
	  source: "scripts/autocomplete.php",//"search.php",
	  minLength: 2/*,
	  select: function( event, ui ) {
		log( ui.item ?
		  "Selected: " + ui.item.value + " aka " + ui.item.id :
		  "Nothing selected, input was " + this.value );
	  }*/
	});
});