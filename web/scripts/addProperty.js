var newLinkLi = '<li></li>';
var addRelationLink = '<a href="#" class="add_relation_link">Add another relation</a>';

jQuery(document).ready(function() {
	//get the ul that holds the collection of relations
	var $collectionHolder = $('ul.relations');
	//add the 'add relation link' to the relations ul
	$collectionHolder.each(function() {
		var $this = $(this);
		$this.data('index', $this.find($('li')).length);
		var $addRelLink = $(addRelationLink);
		var $newLink = $(newLinkLi).append($addRelLink);
		$addRelLink.on('click', function(e){
			//prevent the link from creating a # on the url
			e.preventDefault();
			//add a new relations form
			addRelationForm($this, $newLink);
		});
		$this.append($newLink);
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
