$(function() {
	/*function log( message ) {
	  $( "<div>" ).text( message ).prependTo( "#results" );
	  //$( "#results" ).scrollTop( 0 );
	}*/

	$( "input:first-of-type" ).autocomplete({
	  source: "scripts/autocomplete_name.php",//"search.php",
	  minLength: 2/*,
	  select: function( event, ui ) {
		log( ui.item ?
		  "Selected: " + ui.item.value + " aka " + ui.item.id :
		  "Nothing selected, input was " + this.value );
	  }*/
	});
});