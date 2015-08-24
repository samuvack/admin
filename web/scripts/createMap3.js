/**
 * Created by Berdien De Roo on 21/08/2015.
 */

function createMap(container) {

    //loads the geometries table as a WMS layer
    var untiled = new ol.layer.Image({
        extent: [3.6058864622394333, 50.7489296238961,3.605999818578636, 50.749070669790605],
        source: new ol.source.ImageWMS({
            url:'http://localhost:8080/geoserver/archeowiki/wms',
            params: {'LAYERS': 'archeowiki:geometries'},
            serverType: 'geoserver'
        }),
        visible: true
    });

    //new OL map element in container div with specified options
    var map = new ol.Map({
        controls: [
            new ol.control.Zoom(),
            new ol.control.Attribution(),
            new ol.control.MousePosition()
        ],
        target: container,
        layers: [untiled],
        view: new ol.View({
            center: [3.60593711681199, 50.748987289315],
            maxZoom: 25,
            zoom: 19,
            projection: "EPSG:4326"
        })
    });

    //WMS layer (archeologische zones) from onroerend erfgoed
    var wms = new ol.layer.Image({
            //extent: [-13884991, 2870341, -7455066, 6338219],
            source: new ol.source.ImageWMS({
                //url: 'https://www.mercator.vlaanderen.be/raadpleegdienstenmercatorpubliek/ps/wms',
                url: 'https://geo.onroerenderfgoed.be/geoserver/wms',
                params: {
                    'LAYERS': 'vioe:beschermde_archeologische_zones, vioe:beschermde_dorps_en_stadsgezichten',
                    'TRANSPARENT': 'TRUE',
                    'VERSION':'1.1.1'
                },
                serverType: 'geoserver'
            })
        })
        ;


    map.addLayer(wms);

    //// format used to parse WFS GetFeature responses
    //var geojsonFormat = new ol.format.GeoJSON();
    //
    //var vsStrategyBbox = new ol.source.Vector({
    //    loader: function(extent, resolution, projection) {
    //        var url = 'http://localhost:8080/geoserver/archeowiki/ows?service=WFS&' +
    //            'version=1.1.0&request=GetFeature&typename=archeowiki:geometries&' +
    //            'outputFormat=text/javascript&format_options=callback:loadFeatures' +
    //           // '&srsname=EPSG:4326&bbox=' + extent.join(',') + ',EPSG:4326';
    //        // use jsonp: false to prevent jQuery from adding the "callback"
    //        // parameter to the URL
    //        $.ajax({url: url, dataType: 'jsonp', jsonp: false});
    //    },
    //    //strategy: ol.LoadingStrategy.bbox(),
    //    projection: 'EPSG:4326'
    //});
    //
    //
    ///**
    // * JSONP WFS callback function.
    // * @param {Object} response The response object.
    // */
    //var loadFeatures = function(response) {
    //    vector1.addFeatures(geojsonFormat.readFeatures(response));
    //};
    //
    //
    //// Vector layer
    //var vector1 = new ol.layer.Vector({
    //    source: vsStrategyBbox,
    //    style: new ol.style.Style({
    //        stroke: new ol.style.Stroke({
    //            color: 'green',
    //            width: 2
    //        }),
    //        radius: 5,
    //        fill: new ol.style.Fill({
    //            color:'white'
    //        })
    //    })
    //});

    //map.addLayer(vector1);


    // Add an event handler for the map "singleclick" event
    map.on('singleclick', function(evt) {

        // Hide existing popup and reset it's offset
        var infoDiv = document.getElementById('nodeInfo').innerHTML = '';

        var url = untiled.getSource().getGetFeatureInfoUrl(
            evt.coordinate,
            map.getView().getResolution(),
            map.getView().getProjection(),
            {'INFO_FORMAT': 'text/html'}
        );

        if(url){
            /*$.ajax(url).then(function(response) {
                var parser = new ol.Format.WMSGetFeatureInfo();
                var features = parser.readFeatures(response);
                var text = '';
                for (var i = 0; i < features.length; ++i) {
                    text += features[i].getProperties().props;
                }
                document.getElementById('nodeInfo').innerHTML = text;
            });*/

            document.getElementById('nodeInfo').innerHTML = '<iframe seamless src="' + url + '"></iframe>';
        }

    });
}