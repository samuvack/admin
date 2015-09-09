(function() {
	/*var newLinkLi = '<li></li>';
	var addRelationLink = '<a href="#" class="add_relation_link">Add another relation</a>';*/
	$(document).on('click', '.add_relation_link', function(e) {
		e.preventDefault();
		var $this = $(this);
		var $linkLi = $this.closest('li');
		var $holder = $this.closest('.collectionholder');
		addRelationForm($holder,  $linkLi);
	});
	jQuery(document).ready(function () {
		//get the ul that holds the collection of relations
		initCollectionHolderIndex($('.collectionholder'));
		//add the 'add relation link' to the relations ul

	});

	function addRelationForm($collectionHolder, $newLinkLi) {
		//get the data-prototype
		var prototype = $collectionHolder.data('prototype');
		//get the new index
		var index = $collectionHolder.data('index');
		var placeholder = $collectionHolder.data('placeholder');

		//replace als the '__name__' in the prototype's html to the index number
		var newForm = prototype.replace(new RegExp(placeholder, 'g'), index);

		//increase the index with one
		$collectionHolder.data('index', index + 1);
		//display the form in the page in a li, before the link
		var $newFormLi = $('<li></li>').append(newForm);
		$newLinkLi.before($newFormLi);
		initCollectionHolderIndex($newFormLi.find($('.collectionholder')));
	}

	function initCollectionHolderIndex($collectionHolder) {
		$collectionHolder.each(function () {
			var $this = $(this);
			$this.data('index', $this.find($('li')).length);
		});
	}
})();
