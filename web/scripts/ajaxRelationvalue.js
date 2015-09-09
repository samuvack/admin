function initAjaxFormvalueHook(formurl) {
    $('body').on('change', '.type-selection', function(){
        var $that = $(this);
        var val = $that.val();
        $.ajax({
            type: "GET",
            url: formurl + val,
            success: function(data) {
                var $parent = $that.closest('li > div');
                console.log($parent);
                var id = $parent.attr('id');
                console.log(id);
                var $outer = $("#" + id + "_value");
                var name = $outer.attr('id').replace(/_/,'[');
                name = name.replace(/_/g,'][') +']';
                var html = data.replace(/name="DO_REPLACE_/g, 'name="'+name);
                html = html.replace(/DO_REPLACE_/g, $outer.attr('id'));
                var old = $outer.html();
                $outer.html(html);
            }
        });
    });
}
