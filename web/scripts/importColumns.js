var initDynamicForm = function(columns) {
    var nodeRows = [];

    var updateRelationChange = function(dropdown) {
        var $dropdown = $(dropdown);
        var $row = $dropdown.closest('tr');
        if($dropdown.val() == 0) {
            $row.find('.belongs-to').html('&nbsp;');
            $row.find('.override-value').html('&nbsp;');
        } else {
            var $nodesDropdown = $('<select class="select-parent">');
            for(var key in nodeRows) {
                var $option = $('<option class="parent-'+key+'" value="'+key+'">');
                $nodesDropdown.append($option);
                $option.html(nodeRows[key])
            }
            $row.find('.belongs-to').empty().append($nodesDropdown);
            $row.find('.override-value').html('<input type="text"/>');
        }

        if($dropdown.find(':selected').hasClass('node-property')) {
            var index = $row.attr('id');
            if(!(index in nodeRows)) {
                nodeRows[index] = $row.find('.name').html();
                var $option = $('<option class="parent-'+index+'" value="'+index+'">');
                $option.html(nodeRows[index]);
                $('.select-parent').append($option);
            }
        } else {
            var index = $row.attr('id');
            if(index in nodeRows) {
                delete nodeRows[index];
                $('.parent-'+index).remove();
            }
        }
    };

    $('.property-select').each(function(){
        updateRelationChange($(this));
    }).on('change', function(){
        updateRelationChange(this);
    });
};
