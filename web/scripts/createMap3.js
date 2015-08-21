/**
 * Created by Berdien De Roo on 21/08/2015.
 */

function createMap(container) {

    //new OL map element in container div with specified options
    var map = new ol.Map({
        target: container,
        view: new ol.View({
            center: [50.74895, 3.60590],
            maxZoom: 19,
            zoom: 0,
            projection: "EPSG:4326"
        })
    });


    // format used to parse WFS GetFeature responses
    var geojsonFormat = new ol.format.GeoJSON();

    var vsStrategyBbox = new ol.source.Vector({
        loader: function(extent, resolution, projection) {
            var url = 'http://localhost:8080/geoserver/archeowiki/ows?service=WFS&' +
                'version=1.1.0&request=GetFeature&typename=archeowiki:geometries&' +
                'outputFormat=text/javascript&format_options=callback:loadFeatures' +
               // '&srsname=EPSG:4326&bbox=' + extent.join(',') + ',EPSG:4326';
            // use jsonp: false to prevent jQuery from adding the "callback"
            // parameter to the URL
            $.ajax({url: url, dataType: 'jsonp', jsonp: false});
        },
        //strategy: ol.LoadingStrategy.bbox(),
        projection: 'EPSG:4326'
    });


    /**
     * JSONP WFS callback function.
     * @param {Object} response The response object.
     */
    var loadFeatures = function(response) {
        vector1.addFeatures(geojsonFormat.readFeatures(response));
    };


    // Vector layer
    var vector1 = new ol.layer.Vector({
        source: vsStrategyBbox,
        style: new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: 'green',
                width: 2
            }),
            radius: 5,
            fill: new ol.style.Fill({
                color:'white'
            })
        })
    });


    var wms = new ol.layer.Image({
            //extent: [-13884991, 2870341, -7455066, 6338219],
            source: new ol.source.ImageWMS({
                url: 'https://www.mercator.vlaanderen.be/raadpleegdienstenmercatorpubliek/ps/wms',
                params: {'LAYERS': 'pr_arch'},
                serverType: 'geoserver'
            })
        })
    ;

    map.addLayer(wms);
    map.addLayer(vector1);
}