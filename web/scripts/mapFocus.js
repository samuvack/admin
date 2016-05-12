$(
    $('.focus-map').click(function MapFocus() {
        var ref = 3857;
        var x = $(this).data('x');
        var y = $(this).data('y');
        var layer_id = $(this).data('layer');

        if(window.opener == null) {
            return; // No window for zooming
        }

        window.opener.focusTo(x, y, layer_id);
        window.close();
    })
);
