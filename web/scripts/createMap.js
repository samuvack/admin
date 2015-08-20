/**
 * Created by Berdien De Roo on 20/08/2015.
 */

function createMap(container) {

    //new OL map element in container div with specified options
    //TODO replace bounds by automatically detecting these from input data
    var bounds = new OpenLayers.Bounds(
        3.6058864622394333, 50.7489296238961,
        3.605999818578636, 50.749070669790605
    );
    var options = {
        maxExtent: bounds,
        maxResolution: 0.0000005509605254,
        projection: "EPSG:4326",
        units: 'degrees'
    };
    var map = new OpenLayers.Map(container, options);


    //new blank baselayer
    baseLayer = new OpenLayers.Layer("Blank", {isBaseLayer: true});
    map.addLayer(baseLayer);
    //proxy
    OpenLayers.ProxyHost = "/cgi-bin/proxy.cgi?url=";

    //new stylemap with default and select style
    var defaultStyle = new OpenLayers.Style({
        'pointRadius': 5,
        'fillColor': '#00bc8c',
        'strokeColor': 'white',
        'strokeWidth': 1
    });
    var styleMap = new OpenLayers.StyleMap({
        'default': defaultStyle
    });

    //the geometries layer loaded as wfs via geoserver
    var vector1 = new OpenLayers.Layer.Vector(
        //the name of the layer, e.g. used in the layer switcher
        "nodes", {
            strategies: [new OpenLayers.Strategy.BBOX()],
            projection: "EPSG:4326", //display projection,*/
            protocol: new OpenLayers.Protocol.WFS({
                version: "1.1.0",
                url: "http://localhost:8080/geoserver/archeowiki/wfs",
                featurePrefix: "archeowiki",
                featureType: "geometries",
                featureNS: "http://localhost/archeowiki",
                //geometryName: "geom",
                //srsName: "EPSG:31370"
            }),
            styleMap: styleMap
        }
    );
    //add layer to the map and zoom to max extent of the layers
    map.addLayer(vector1);
    map.zoomToMaxExtent();
}