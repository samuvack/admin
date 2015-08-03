var $type = $('#filter_type');
// When type gets selected...
$type.change(function() {
	//retrieve the corresponding form
	var $form = $(this).closest('form');
	//simulate form data, but only include the selected type values
	var data = {};
	data[$type.attr('name')] = $type.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    success: function(html) {
      // Replace current position field ...
      $('#filter_property').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html).find('#filter_property')
      );
      // Position field now displays the appropriate positions.
    }
  });
});
