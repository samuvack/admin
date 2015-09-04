var initDynamicForm = function(columns) {
    var nodeRows = {};

    var updateRelationChange = function(dropdown) {
        var $dropdown = $(dropdown);
        var $row = $dropdown.closest('tr');
        if($dropdown.val() == 'ROOT' || $dropdown.val() == 'EMPTY') {
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
            var index = $row.attr('id').replace(/\D/g,'');
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
    $('#column-form').submit(function(){
        var $this = $(this);
        var postParam = {};
        $(this).find('tr.import-column').each(function(){
            var $that = $(this);
            var relationtype = $that.find('.property-select').val();
            if(relationtype == 'EMPTY')
                return true; // continue;
            var column = parseInt($that.attr('id').replace(/\D/g,''));
            postParam[column] = {};
            postParam[column]['type'] = relationtype;
            if(relationtype !== 'ROOT') {
                postParam[column]['belongsTo'] = $that.find('.belongs-to .select-parent').val();
            }
            var $override =  $that.find('.override-value input');
            if($override.length > 0) {
                var valueOverride = $that.find('.override-value input').val().trim();
                if (valueOverride.length > 0)
                    postParam[column]['value'] = valueOverride;
            }
        });
        $('#columns-json').val(JSON.stringify(postParam));
    });

    $('.property-select').each(function(){
        updateRelationChange($(this));
    }).on('change', function(){
        updateRelationChange(this);
    });
};
