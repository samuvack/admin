var collectionHolder;

// setup an "add a tag" link
var $addRelationLink = $('<a href="#" class="add_relation_link">Add another relation</a>');
var $newLinkLi = $('<li></li>').append($addRelationLink);

jQuery(document).ready(function() {
	//get the ul that holds the collection of relations
	$collectionHolder = $('ul.relations');
	//add the 'add relation link' to the relations ul
	$collectionHolder.append($newLinkLi);
	
	//count the current list items, but decrease one as link is also list item, use that as the new inserting a new item
	$collectionHolder.data('index', $collectionHolder.find($('li')).length - 1);
	
	$addRelationLink.on('click', function(e){
		//prevent the link from creating a # on the url
		e.preventDefault();
		//add a new relations form
		addRelationForm($collectionHolder, $newLinkLi);
	});
});

function addRelationForm($collectionHolder, $newLinkLi) {
	//get the data-prototype
	var prototype = $collectionHolder.data('prototype');
	//get the new index
	var index = $collectionHolder.data('index');
	
	//replace als the '__name__' in the prototype's html to the index number
	var newForm = prototype.replace(/__name__/g, index);
	
	//increase the index with one
	$collectionHolder.data('index', index + 1);
	//display the form in the page in a li, before the link
	var $newFormLi = $('<li></li>').append(newForm);
	$newLinkLi.before($newFormLi);
}