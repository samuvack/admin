/**
 * Created by Berdien De Roo on 21/08/2015.
 */

function createMap(container) {

    var geojsonFormat = new ol.format.GeoJSON();
    var vectorSource = new ol.source.Vector({
        loader: function (extent, resolution, projection) {

            var url = '/../cgi-bin/proxy.cgi?url=' + encodeURIComponent("http://localhost:8080/geoserver/archeowiki/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=archeowiki:geometries&maxFeatures=50&outputFormat=application%2Fjson");

            $.ajax(url).then(function (response) {
                var features = geojsonFormat.readFeatures(response, {
                    featureProjection: projection
                });
                vectorSource.addFeatures(features);
            });
        }
    });

    var untiled = new ol.layer.Vector({
        source : vectorSource
    });

    vectorSource.clear(true);

    //loads the geometries table as a WMS layer
    /*var untiled = new ol.layer.Image({
        extent: ol.extent.applyTransform(
            [3.6058864622394333, 50.7489296238961,3.605999818578636, 50.749070669790605],
            ol.proj.getTransform('EPSG:4326','EPSG:3857')
        ),
        source: new ol.source.ImageWMS({
            url:'http://localhost:8080/geoserver/archeowiki/wms',
            params: {'LAYERS': 'archeowiki:geometries'},
            serverType: 'geoserver'
        }),
        visible: true
    });*/

    //WMS layer (CAI) from onroerend erfgoed (enkel voor geregistreerde cai gebruikers)
    /*var cai = new ol.layer.Image({
            source: new ol.source.ImageWMS({
                url: 'https://geo.onroerenderfgoed.be/geoserver/wms',
                params: {
                    'LAYERS': 'vioe_intern:cai',
                    'TRANSPARENT': 'TRUE',
                    'VERSION':'1.1.1'
                },
                serverType: 'geoserver'
            })
        })
        ;*/

    //WMS layer (Archeologische zones) from onroerend erfgoed
    var archZones = new ol.layer.Image({
            source: new ol.source.ImageWMS({
                url: 'https://geo.onroerenderfgoed.be/geoserver/wms',
                params: {
                    'LAYERS': 'vioe:cai_zone',
                    'STYLES': 'vioe_cai_zones',
                    'TRANSPARENT': 'TRUE',
                    'VERSION':'1.1.1'
                },
                serverType: 'geoserver'
            })
        })
        ;

    var osm = new ol.layer.Tile({
        source: new ol.source.OSM()
    });

    //new OL map element in container div with specified options
    var map = new ol.Map({
        controls: [
            new ol.control.Zoom(),
            new ol.control.Attribution(),
            new ol.control.MousePosition({
                projection: 'EPSG:4326',
                coordinateFormat: ol.coordinate.createStringXY(4)}),
            new ol.control.ZoomToExtent({extent: untiled.getExtent()}),
            new ol.control.ScaleLine()
        ],
        target: container,
        layers: [osm, untiled, archZones],
        view: new ol.View({
            center: ol.proj.transform([3.60593711681199, 50.748987289315], 'EPSG:4326', 'EPSG:3857'),
            maxZoom: 25,
            zoom: 19
        })
    });



    //create 3d globe view
    var ol3d = new olcs.OLCesium({
        map: map,
        target: '3dmap'
    });
    var scene = ol3d.getCesiumScene();
    var terrainProvider = new Cesium.CesiumTerrainProvider({
        url: '//cesiumjs.org/stk-terrain/tilesets/world/tiles'
    });
    scene.terrainProvider = terrainProvider;
    ol3d.setEnabled(true);


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

        // Clear existing information in nodeInfo div
        var infoDiv = document.getElementById('nodeInfo').innerHTML = '';

        //TODO: change to allow multiple layers to be queried
        var url = archZones.getSource().getGetFeatureInfoUrl(
            evt.coordinate,
            map.getView().getResolution(),
            map.getView().getProjection(),
            {'INFO_FORMAT': 'text/html'}
        );

        //TODO: change from iframe to display in text
        if(url){
            document.getElementById('nodeInfo').innerHTML = '<iframe seamless src="' + url + '"></iframe>';
        }

    });
}