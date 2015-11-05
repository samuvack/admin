/**
 * Created by Berdien De Roo on 21/08/2015.
 */
(function() {
    var container = 'map';
    var geojsonFormat = new ol.format.GeoJSON();
    var vectorSource = new ol.source.Vector({
        loader: function (extent, resolution, projection) {

            var url = '/cgi-bin/proxy.cgi?url=' + encodeURIComponent("http://localhost:8080/geoserver/archeowiki/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=archeowiki:geometries&maxFeatures=50&outputFormat=application%2Fjson");



            $.ajax(url).then(function (response) {
                var features = geojsonFormat.readFeatures(response, {
                    featureProjection: projection
                });
                vectorSource.addFeatures(features);
            });

        }
    });

    var untiled = new ol.layer.Vector({
        source: vectorSource
    });


    //allows the loader function to be called
    vectorSource.clear(true);

    /*

    VOORBEELD BERDIEN
    //WMS layer (Archeologische zones) from onroerend erfgoed
    var archZones = new ol.layer.Image({
            source: new ol.source.ImageWMS({
                url: '/cgi-bin/proxy.cgi?url='+ encodeURIComponent('https://geo.onroerenderfgoed.be/geoserver/wms'),
                params: {
                    'LAYERS': 'vioe:cai_zone',
                    'STYLES': 'vioe_cai_zones',
                    'TRANSPARENT': 'TRUE',
                    'VERSION': '1.1.1'
                },
                serverType: 'geoserver'
            })
        })
        ;

    */

    /*

     VOORBEELD BART DE WIT
   */





var ship = new ol.layer.Tile({
    title: 'Scheepswrakken',
    extent: [250000, 6630000, 500000, 6770000],
    source: new ol.source.TileWMS({
        url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
        params: {'LAYERS': 'ships'},
        serverType: 'geoserver'
    }),
    visible:false
});



     var plains = new ol.layer.Image({
            title: 'Vliegtuigwrakken',
            extent: [250000, 6630000, 500000, 6770000],
            source: new ol.source.ImageWMS({
            url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
            params: {'LAYERS': 'plains'},
            serverType: 'geoserver'
            }),
     visible:false
     });



     var artefacts = new ol.layer.Image({
     title: 'Artefacts',
     extent: [250000, 6630000, 500000, 6770000],
     source: new ol.source.ImageWMS({
     url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
     params: {'LAYERS': 'artefacts'},
     serverType: 'geoserver'
     }),
     visible:false
     });


     var structures = new ol.layer.Image({
     title: 'Structures',
     extent: [250000, 6630000, 500000, 6770000],
     source: new ol.source.ImageWMS({
     url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
     params: {'LAYERS': 'structures'},
     serverType: 'geoserver'
     }),
     visible:false
     });


      var events = new ol.layer.Image({
        title: 'Events',
        extent: [250000, 6630000, 500000, 6770000],
        source: new ol.source.ImageWMS({
        url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
        params: {'LAYERS': 'events'},
        serverType: 'geoserver'
     }),
     visible:false
     });


     var others = new ol.layer.Image({
     title: 'Andere',
     extent: [250000, 6630000, 500000, 6770000],
     source: new ol.source.ImageWMS({
     url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
     params: {'LAYERS': 'others'},
     serverType: 'geoserver'
     }),
     visible:false
     });


     var contplaat = new ol.layer.Image({
     title: 'Belgisch Continentale PLaat',
     extent: [250000, 6630000, 500000, 6770000],
     source: new ol.source.ImageWMS({
     url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
     params: {'LAYERS': 'belgisch_continentaal_plat'},
     serverType: 'geoserver'
     }),
     visible:true
     });


     var zandbanken = new ol.layer.Image({
     title: 'Zandbanken',
     extent: [250000, 6630000, 500000, 6770000],
     source: new ol.source.ImageWMS({
     url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
     params: {'LAYERS': 'sandbanks_nl_en'},
     serverType: 'geoserver'
     }),
     visible:true
     });


     var toppaleogenemorphology = new ol.layer.Image({
     title: 'Top paleogenemorphology',
     extent: [250000, 6630000, 500000, 6770000],
     source: new ol.source.ImageWMS({
     url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
     params: {'LAYERS': 'toppaleogenemorphology_nl_en'},
     serverType: 'geoserver'
     }),
     visible:false
     });


     var archaeology = new ol.layer.Image({
     title: 'Archeology',
     extent: [250000, 6630000, 500000, 6770000],
     source: new ol.source.ImageWMS({
     url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
     params: {'LAYERS': 'archaeology'},
     serverType: 'geoserver'
     }),
     visible:false
     });


     var zeebodem = new ol.layer.Image({
     title: 'Zeebodem',
     extent: [250000, 6630000, 500000, 6770000],
     source: new ol.source.ImageWMS({
     url: 'http://we12s007.ugent.be:8080/geoserver/search/wms',
     params: {'LAYERS': 'zeebodem'},
     serverType: 'geoserver'
     }),
     visible:false
     });



       var OpenStreetMap = new ol.layer.Tile({
            title: 'OpenStreet Map',
            type: 'base',
            visible: true,
            source: new ol.source.OSM()
        });

    var Watercolor =  new ol.layer.Tile({
        title: 'Water Color',
        type: 'base',
        visible: false,
        source: new ol.source.Stamen({
            layer: 'watercolor'
        })
    });



       var Satellite = new ol.layer.Tile({
            title: 'Satellite',
            type: 'base',
            visible: false,
            source: new ol.source.MapQuest({layer: 'sat'})
        });

    var Hybride = new ol.layer.Tile({
        title: 'Hybride',
        type: 'base',
        visible: false,
        source: new ol.source.MapQuest({layer: 'hyb'})
    });




        var osm = new ol.layer.Tile({
            source: new ol.source.OSM()
         });

             //new OL map element in container div with specified options
             var map = new ol.Map({
                 controls: [
                     new ol.control.Zoom(),
                     //new ol.control.Attribution(),

                     new ol.control.ZoomToExtent({
                         extent: [
                             250000, 6630000,
                             500000, 6770000
                         ]
                     }),

                    new ol.control.OverviewMap(),

                     new ol.control.MousePosition({
                         projection: 'EPSG:4326',
                         coordinateFormat: ol.coordinate.createStringXY(4)
                     }),
                     new ol.control.Rotate(),
                     new ol.control.ScaleLine(),

                 ],
                 target: container,

                 //toevoegen aan kaart
                 layers: [
                     new ol.layer.Group({
                         'title': 'Base maps',

                         layers: [OpenStreetMap, Watercolor, Satellite, Hybride]
                     }),

                     new ol.layer.Group({
                         'title': 'Dieptemodel',
                         layers: [ zeebodem ]
                     }),

                     new ol.layer.Group({
                         'title': 'Situering',
                         layers: [ toppaleogenemorphology,zandbanken, contplaat]
                     }),


                     new ol.layer.Group({
                         'title': 'Archeologische vondsten',
                         layers: [ archaeology]
                     }),

                     new ol.layer.Group({
                         'title': 'Erfgoed',
                         layers: [ ship, plains,artefacts, structures, events, others ]
                     }),



    ],

                 view: new ol.View({
                     center: ol.proj.transform([2.7462, 51.5108], 'EPSG:4326' , 'EPSG:3857' ),
                     maxZoom: 25,
                     zoom: 9.5
                 })
             });


             var layerSwitcher = new ol.control.LayerSwitcher({
                 tipLabel: 'Legend' // Optional label for button
             });
             map.addControl(layerSwitcher);













    //create 3d globe view
    var ol3d = new olcs.OLCesium({
        map: map,
        target: '3dmap'
    });
    /*commented terrainprovider since problem with displaying features
     var scene = ol3d.getCesiumScene();
     var terrainProvider = new Cesium.CesiumTerrainProvider({
     url: '//cesiumjs.org/stk-terrain/tilesets/world/tiles'
     });
     scene.terrainProvider = terrainProvider;*/

    ol3d.setEnabled(true);


   /* map.on('singleclick', function(evt) {
        document.getElementById('info').innerHTML = '';
        var viewResolution = // @type {number} // (view.getResolution());
        var url = ship.getGetFeatureInfoUrl(
            evt.coordinate, viewResolution, 'EPSG:3857',
            {'INFO_FORMAT': 'text/html'});
        if (url) {
            document.getElementById('info').innerHTML =
                '<iframe seamless src="' + url + '"></iframe>';
        }
    });

    map.on('singleclick', function(evt) {
        document.getElementById('info').innerHTML = '';
        var viewResolution = // @type {number} // (view.getResolution());
        var url = Satellite.getGetFeatureInfoUrl(
            evt.coordinate, viewResolution, 'EPSG:3857',
            {'INFO_FORMAT': 'text/html'});
        if (url) {
            document.getElementById('info').innerHTML =
                '<iframe seamless src="' + url + '"></iframe>';
        }
    });

*/


    // Add an click event handler for the map which displays the id/info and styles the feature
    var selectedFeature;
    map.on('click', function (evt) {
        document.getElementById('info').innerHTML = '';
        if (selectedFeature) {
            selectedFeature.setStyle(null);
        }

        selectedFeature = map.forEachFeatureAtPixel(
            evt.pixel,
            function (feature, layer) {
                return feature;
            }
        );

        if (selectedFeature) {
            selectedFeature.setStyle(new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 7,
                    fill: new ol.style.Fill({color: '#00bc8c'}),
                    stroke: new ol.style.Stroke({color: 'black', width: 2})
                })
            }));
            var geoId = selectedFeature.getId().replace(/\D+/g, "");
            showNodeInfo(geoId);
            //document.getElementById('nodeInfo').innerHTML = 'Selected: ' + selectedFeature.getId();
        } else {
            //If no feature is selected, try to get wms info
            //TODO: change to allow querying multi layers
            var url = plains.getSource().getGetFeatureInfoUrl(
                evt.coordinate,
                map.getView().getResolution(),
                map.getView().getProjection(),
                {'INFO_FORMAT': 'text/html'}
            );

            //TODO: change from iframe to display in text
            if (url) {
                document.getElementById('info').innerHTML = '<iframe seamless src="' + url + '"></iframe>';
            }
        }
    });


/*
    //Add event handler to the cesium map to allow selecting features, style and show id
    var giveInfoHandler = new Cesium.ScreenSpaceEventHandler(ol3d.getCesiumScene().canvas);
    giveInfoHandler.setInputAction(
        function (movement) {
            document.getElementById('nodeInfo').innerHTML = '';
            if (selectedFeature) {
                selectedFeature.setStyle(null);
            }
            var posit = movement.position;
            var pickedObject = ol3d.getCesiumScene().pick(posit);
            if (Cesium.defined(pickedObject)) {
                var x = pickedObject.primitive.olFeature.getId();
                selectedFeature = vectorSource.getFeatureById(x);
                selectedFeature.setStyle(new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 7,
                        fill: new ol.style.Fill({color: '#00bc8c'}),
                        stroke: new ol.style.Stroke({color: 'black', width: 2})
                    })
                }));
                var geoId = selectedFeature.getId().replace(/\D+/g, "");
                showNodeInfo(geoId);
            }
        },
        Cesium.ScreenSpaceEventType.LEFT_CLICK
    );

    function showNodeInfo(id) {
        //ajax request to get the node info of the selected geometry
        var currUrl = window.location.href;
        var url = currUrl.substring(0,currUrl.search('/map')) + '/ajax/nodeInfoByGeo/';
        //TODO: color selected node different
        //TODO: change url to {{basepath
        $.get(
            url + id,
            null,
            function(data) {
                var $info = $('#nodeInfo');
                $info.html(data);
            }
        );
    };

    $("#2dbutton").click(function() {
        ol3d.setEnabled(true);
        $("#map").css({
            "width": "80%",
            "display": 'block'
        });
        map.updateSize();
        $("#3dmap").css("display","none");
    });

    $("#3dbutton").click(function() {
       ol3d.setEnabled(false);
        $("#map").css("display","none");
        $("#3dmap").css({
            "width":"80%",
            "display":"block"
        });
    });

    $("#2d3dbutton").click(function() {
        ol3d.setEnabled(false);
        $("#map").css({
            "width":"40%",
            "display":"block"
        });
        map.updateSize();
        $("#3dmap").css({
            "width":"40%",
            "display":"block"
        });
    });

    $("#mapExport").click(function() {
        map.once('postcompose', function(event) {
            var canvas = event.context.canvas;
            $("#mapExport").attr("href",canvas.toDataURL('image/png'));
        });
        map.renderSync();



    })
*/

    map.on('click', function(evt) {
        document.getElementById('info2').innerHTML = '';
        var viewResolution = /** @type {number} */ (view.getResolution());
        var url = wmsSource.getGetFeatureInfoUrl(
            evt.coordinate, viewResolution, 'EPSG:4326',
            {'INFO_FORMAT': 'text/html'});
        if (url) {
            document.getElementById('info2').innerHTML =
                '<iframe seamless src="' + url + '"></iframe>';
        }
    });

    map.on('singleclick', function(evt){
        var coord = evt.coordinate;
        var transformed_coordinate = ol.proj.transform(coord, "EPSG:4326", "EPSG:3857");
        console.log(transformed_coordinate);
    })

    //pop up
    map.on('singleclick', function(evt){
        var coord = evt.coordinate;
        spawnPopup(coord);
    });

    function spawnPopup(coord){
        var popup = $("<div class='popup'></div>");

        var overlay = new ol.Overlay({
            element:popup
        });

        map.addOverlay(overlay);
        overlay.setPosition(coord);
    }


})();
