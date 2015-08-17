/**
 * Created by Berdien De Roo on 17/08/2015.
 * Improved by David Vandorpe on 18/08/2015
 *      - Based on WiNA's notorious muilgraaf, written by Lorin Werthen and David Vandorpe (ZeusWPI pride)
 *          - Yes, this is a thing.
 */

function createGraph(nodes, links) {


    //append an svg canvas to a DOM element
    var vis = d3.select('#graph')
        .append("svg");
    //change width and height
    var w = 900;
    var h = 400;
    vis.attr("width", w)
        .attr("height", h);

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

//use force directed layout algorithm
    var force = d3.layout.force()
        .size([w - 50, h - 50])
        .charge(-100)
        .linkDistance(20)
        .nodes(nodes)
        .links(links)
        .start();

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
        .attr("class", "node");

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
}
