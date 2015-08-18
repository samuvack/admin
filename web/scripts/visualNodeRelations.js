/**
 * Created by Berdien De Roo on 17/08/2015.
 * Improved by David Vandorpe on 18/08/2015
 *      - Based on WiNA's notorious muilgraaf, written by Lorin Werthen and David Vandorpe (ZeusWPI pride)
 *          - Yes, this is a thing.
 */

d3.selection.prototype.moveToFront = function () {
    return this.each(function () {
        this.parentNode.appendChild(this);
    });
};

function createGraph(nodes, links, svgSelector) {


    //select svg canvas (width and height set in css)
    var svg = d3.select(svgSelector);

    //add a g element as group container
    var vis=svg.append('g');

    //construct new zoom behavior
    var zoom = d3.behavior.zoom()
        .scaleExtent([0.5, 10])
        .on("zoom", zoomed);
    //apply behavior to svg-element
    svg.call(zoom);

    var $wrapper = $(svg[0][0]);

    var width = $wrapper.width(),
        height = $wrapper.height();

    //define nodes
    var nodeMap = {};
    nodes.forEach(function (d) {
        d.id = +d.id;
        nodeMap[d.id] = d;
    });

    //ensure linkage between source/target of link and id of nodes
    links.forEach(function (d) {
        d.source = nodeMap[d.source];
        d.target = nodeMap[d.target]
    });

    //use force directed layout algorithm for the graph
    var force = d3.layout.force()
        .gravity(.05)
        .charge(-100)
        .distance(150)
        .linkDistance(20)
        .size([width - 50, height - 50])
        .nodes(nodes)
        .links(links)
        .start();

    //bind behavior to nodes to allow interactive dragging
    var drag = force.drag()
        .origin(function (d) {
            return d;
        })
        .on("dragstart", function () {
            d3.event.sourceEvent.stopPropagation();
        })
        .on("drag", dragged);

    //visualize links as line
    var link = vis.selectAll(".link")
        .data(links)
        .enter().append("line")
        .attr("class", "link")
        .style("stroke", "#00bc8c")
        .style("stroke-width", "5px");

    var linkMap = {};
    link.each(function (d, i) {
        linkMap[d.source.id + "," + d.target.id] = this;
        linkMap[d.target.id + "," + d.source.id] = this;
    });

    //visualize nodes as g eleements consisting of circle and text
    //add drag functionality to nodes via .call
    var node = vis.selectAll(".node")
        .data(nodes, function (d) {
            return d.id
        })
        .enter().append("g")
        .attr("class", "node")
        .on("mouseover", function (d) {
            highlight(d, d3.select(this));
        })
        .on("mouseout", function (d) {
            unhighlight(d3.select(this));
        })
        .call(drag);

    node.append("circle")
        .attr("r", "5px")
        .attr("fill", "white");

    node.append("text")
        .attr("dx", 12)
        .attr("dy", ".35em")
        .text(function (d) {
            return d.name
        })
        .attr("pointer-events", "none")
        .style("font-size", "12px")
        .style("fill", "white");

    //update the displayed positions of nodes and links on every tick of the simulation
    force.on("tick", function () {
        link.attr("x1", function (d) {
            return d.source.x;
        })
            .attr("y1", function (d) {
                return d.source.y;
            })
            .attr("x2", function (d) {
                return d.target.x;
            })
            .attr("y2", function (d) {
                return d.target.y;
            });

        node.attr("transform", function (d) {
            return "translate(" + d.x + "," + d.y + ")";
        })
    });

    // Zoom functionality
    function zoomed() {
        vis.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
    }

    // Dragging functionality
    function dragged(d) {
         d3.select(this).attr(function (d) {
            return "translate(" + d.x + "," + d.y + ")";
         });
    }

    //highlights the node and its neighbouring nodes and links by changing opacity of others
    function highlight(data, cur) {
        //change class of selected node
        cur.classed("selected", true);
        cur.moveToFront();

        //change opacity of all links and all nodes which are not of class selected
        svg.selectAll(".node:not(.selected)").attr("opacity", .2);
        link.attr("stroke-opacity", .2);

        // Highlight neighbouring nodes
        node.each(function (d) {
            var neighbour = d3.select(this);

            neighbour.moveToFront();
            if (linkMap[data.id + "," + d.id] !== undefined) {
                d3.select(linkMap[data.id + "," + d.id]).attr("stroke-opacity", 1);
                neighbour.attr("opacity", 1);
            }
        });
    }

    //unhighlights
    function unhighlight(cur) {
        //change class of previously selected node
        cur.classed("selected", false);

        //change opacity of all nodes and links to 1
        node.attr("opacity", 1.0);
        link.attr("stroke-opacity", 1);
    }
}
