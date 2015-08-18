/**
 * Created by Berdien De Roo on 17/08/2015.
 * Improved by David Vandorpe on 18/08/2015
 *      - Based on WiNA's notorious muilgraaf, written by Lorin Werthen and David Vandorpe (ZeusWPI pride)
 *          - Yes, this is a thing.
 */

function createGraph(nodes, links, svgSelector) {


    //append an svg canvas to a DOM element
    var svg = d3.select(svgSelector);
    //change width and height
    /*var w = 900;
    var h = 400;
    vis.attr("width", w)
        .attr("height", h);*/
    var vis=svg.append('g');

    var zoom = d3.behavior.zoom()
        .scaleExtent([0.5, 10])
        .on("zoom", zoomed);
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

    links.forEach(function (d) {
        d.source = nodeMap[d.source];
        d.target = nodeMap[d.target]
    });

    var force = d3.layout.force()
        .gravity(.05)
        .charge(-100)
        .distance(150)
        .linkDistance(20)
        .size([width - 50, height - 50])
        .nodes(nodes)
        .links(links)
        .start();

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

//visualize nodes as circle with label
    var node = vis.selectAll(".node")
        .data(nodes, function (d) {
            return d.id
        })
        .enter().append("g")
        .attr("class", "node")
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
    function dragstarted(d) {
        d3.event.sourceEvent.stopPropagation();
    }

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
}
