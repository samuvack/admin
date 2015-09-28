function makeAdminConsole(graphhandler, baseUrl) {
    $('#resetConsole').click(function() {
        $('#resetConsole').hide();
        $("#addLinks").show();
        graphHandler.resetNodeListener();
    });

    $("#addLinks").click(function () {
        //show button to stop adding links and remove the add links
        $('#resetConsole').show();
        $("#addLinks").hide();
        //set handlers
        graphHandler.removeNodeListener();
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
                graphHandler.addLink(node, first.node, 'is part of');
                d3.select(first.dom).classed("colored", false);
                first = null;
            } else {
                d3.select(this).classed("colored", false);
                first = null;
            }
        });
    });
}
