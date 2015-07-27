//$(function() {
	var collectionHolder;
	
	// setup an "add a tag" link
	var $addRelationLink = $('<a href="#" class="add_relation_link">Add another relation</a>');
	var $newLinkLi = $('<li></li>').append($addRelationLink);
	
	jQuery(document).ready(function() {
		$collectionHolder = $('ul.relations');
		$collectionHolder.append($newLinkLi);
		
		$collectionHolder.data('index', $collectionHolder.find('inout').length);
		
		$addRelationLink.on('click', function(e){
			e.preventDefault();
			addRelationForm($collectionHolder, $newLinkLi);
		});
	});
	
	function addRelationForm($collectionHolder, $newLinkLi) {
		var prototype = $collectionHolder.data('prototype');
		var index = $collectionHolder.data('index');
		//var newForm = prototype.replace(/___name___/g, index);
		var newForm = prototype.replace(/__name__/g, index);
		$collectionHolder.data('index', index + 1);
		var $newFormLi = $('<li></li>').append(newForm);
		$newLinkLi.before($newFormLi);
	}
//});