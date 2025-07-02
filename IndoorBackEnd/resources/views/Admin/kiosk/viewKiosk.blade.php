@extends('layouts.adminLayouts')
@section('admin-title', 'View Kiosk')
@section('leaflet-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endsection
@section('admin-content')
    <section class="section">
        <div id="map" style="height: 400px;"></div>
    </section>
@endsection
@section('leaflet-js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        var lotsLabel = [];
        var map = L.map('map').setView([51.505, -0.09], 13);
        map.on("zoomend", function() {
            var currentZoom = map.getZoom();

            if (currentZoom >= 19) {
                lotsLabel.forEach(function(marker) {
                    marker.addTo(map);
                });
            } else {
                lotsLabel.forEach(function(marker) {
                    map.removeLayer(marker);
                });
            }
        });

        @foreach ($kiosks as $kiosk)
            L.marker([{{ $kiosk->latitude }}, {{ $kiosk->longitude }}]).addTo(map)
                .bindPopup('<b>{{ $kiosk->name }}</b><br>{{ $kiosk->address }}');
        @endforeach
        @foreach ($point as $markKiosk)
            var latlng = L.latLng({{ $markKiosk->latitude }}, {{ $markKiosk->longitude }});
            var marker = L.marker(latlng).addTo(map);
        @endforeach
        var geojsonFeature = {!! $results->toJson() !!};
        const mainMapLotsGeojson = {
            type: "FeatureCollection",
            features: geojsonFeature
                .filter((lot) => lot.geojson)
                .map((lot) => {

                    return {
                        type: "Feature",
                        geometry: JSON.parse(lot.geojson),
                        properties: {
                            levelcode: lot.levelcode,
                            category: lot.category,
                            storename: lot.storename,
                        },
                    };

                })
                .filter((feature) => feature !== null),
        };
        console.log("GeoJSON Feature:", mainMapLotsGeojson);
        var mainMapLotsGeoJsonLayer = L.geoJSON(mainMapLotsGeojson, {
            style: setFeatureStyle,
            onEachFeature: function(feature, layer) {
                layer.on({
                    mouseover: highlightFeature,
                    mouseout: resetHighlight,
                });
                var label = L.marker(layer.getBounds().getCenter(), {
                    icon: L.divIcon({
                        className: "label",
                        html: feature.properties.storename,
                        iconSize: [100, 40],
                    }),
                });
                lotsLabel.push(label);
            },
        }).addTo(map);

        const layerBounds = mainMapLotsGeoJsonLayer.getBounds();
        map.setMinZoom(
            map.getBoundsZoom(mainMapLotsGeoJsonLayer.getBounds())
        );
        map.setMaxBounds(layerBounds);
        map.on("drag", function() {
            map.panInsideBounds(layerBounds, {
                animate: false
            });
        });

        console.log("GeoJSON Layer Bounds:", mainMapLotsGeoJsonLayer.getBounds());
        var bounds = mainMapLotsGeoJsonLayer.getBounds();
        if (bounds.isValid()) {
            map.fitBounds(bounds);
        } else {
            console.error("Invalid bounds:", bounds);
        }

        function highlightFeature(e) {
            var layer = e.target;
            layer.setStyle({
                weight: 8,
                color: "#666",
                dashArray: "",
                fillOpacity: 0.7,
            });
        }

        function resetHighlight(e) {
            var layer = e.target;
            let style = setFeatureStyle(layer.feature);
            layer.setStyle(style);
        }

        function setFeatureStyle(feature) {
            if (feature.properties.category != null) {
                return {
                    fillColor: "#F6F1EE",
                    weight: 2,
                    opacity: 1,
                    color: "white",
                    fillOpacity: 0.8,
                };
            } else {
                return {
                    fillColor: "#A0153E",
                    weight: 2,
                    opacity: 1,
                    color: "white",
                    fillOpacity: 0.5,
                };
            }
        }
    </script>



@endsection
