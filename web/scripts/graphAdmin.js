function makeAdminConsole(graphhandler, baseUrl) {
    $('#resetConsole').click(function() {
        graphHandler.resetNodeListener();
    });

    $("#addLinks").click(function () {
        graphHandler.resetNodeListener();
        var first = null;
        graphHandler.setNodeEvent(function (node) {
            if (first === null) {
                first = {dom: this, node: node};
                d3.select(this).classed("colored", true);
            } else if (node.id !== first.node.id) {
                var url =baseUrl + "/ajax/addGraphLink/"+first.node.nodeid+"/"+node.nodeid;
                $.ajax({
                    url: url ,
                    type: 'GET',
                    error: function (data) {
                        alert("Something went wrong and the database probably isn't updated!");
                    }

                });
                graphHandler.addLink(node, first.node, 'is_part_of');
                d3.select(first.dom).classed("colored", false);
                first = null;
            } else {
                d3.select(this).classed("colored", false);
                first = null;
            }
        });
    });
}
