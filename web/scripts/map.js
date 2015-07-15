function() {
	//new OL map element in map div
	var map = new OpenLayers.Map("map");
	//new blank baselayer
	baseLayer = new OpenLayers.Layer("Blank", {isBaseLayer:true});
	map.addLayer(baseLayer);
	//proxy
	OpenLayers.ProxyHost = "/cgi-bin/proxy.cgi?url="
	
	//new stylemap with default and select style
	var defaultStyle = new OpenLayers.Style({
		'fill': '#A0C9DE',
		'strokeColor': '#DEB5A0',
		'strokewidth': 1
	});
	var styleMap = new OpenLayers.Style({
		'default': defaultStyle
	});
	
	//the geometries layer loaded as wfs via geoserver
	var vector1= new OpenLayers.Layer.Vector(
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
		styleMap:styleMap
		}
	);
	//add layer to the map and zoom to max extent of the layers
	map.addLayer(vector1);
	map.zoomToMaxExtent();
};