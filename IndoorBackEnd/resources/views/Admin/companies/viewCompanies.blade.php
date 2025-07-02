@extends('layouts.adminLayouts')
@section('admin-title', 'View Companies')
@section('leaflet-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endsection
@section('admin-content')
    <section class="section">
        <div class="section-body">
            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card author-box">
                        <div class="card-body">
                            <div class="author-box-center">
                                <img alt="image" src="data:image/png;base64,{{ $companies->first()->image_data }}"
                                    {{-- <img alt="image" src="{{ asset($companies->first()->images) }}" --}} class="rounded-circle author-box-picture">
                                <div class="clearfix"></div>
                                <div class="author-box-name">
                                    <a href="#">{{ $companies->first()->storename }}</a>
                                </div>
                                <div class="author-box-job">{{ $companies->first()->sub_cat }}</div>
                            </div>
                            {{-- <div class="text-center">
                                <div class="author-box-description">
                                    <p>
                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur voluptatum alias
                                        molestias
                                        minus quod dignissimos.
                                    </p>
                                </div>
                                <div class="mb-2 mt-3">
                                    <div class="text-small font-weight-bold">Follow Hasan On</div>
                                </div>
                                <a href="#" class="btn btn-social-icon mr-1 btn-facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="btn btn-social-icon mr-1 btn-twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-social-icon mr-1 btn-github">
                                    <i class="fab fa-github"></i>
                                </a>
                                <a href="#" class="btn btn-social-icon mr-1 btn-instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <div class="w-100 d-sm-none"></div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Personal Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="py-4">
                                <p class="clearfix">
                                    <span class="float-left">
                                        Days
                                    </span>
                                    <span class="float-right text-muted">
                                        {{ $companies->first()->day }} </span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">
                                        Phone
                                    </span>
                                    <span class="float-right text-muted">
                                        {{ $companies->first()->phone }}
                                    </span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">
                                        Timing
                                    </span>
                                    <span class="float-right text-muted">
                                        {{ $companies->first()->time }}
                                    </span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">
                                        website
                                    </span>
                                    <span class="float-right text-muted">
                                        <a target="_blank"
                                            href="{{ $companies->first()->website }}">{{ $companies->first()->website }}</a>
                                    </span>
                                </p>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div id="map" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

        @foreach ($companies as $kiosk)
            var icon = L.icon({
                iconUrl: 'data:image/png;base64,{{ $kiosk->image_data }}',
                iconSize: [50, 50],
                iconAnchor: [25, 50],
                popupAnchor: [0, -50],
            });
            L.marker([{{ $kiosk->latitude }}, {{ $kiosk->longitude }}], ).addTo(map)
                .bindPopup('<b>{{ $kiosk->storename }}</b><br>{{ $kiosk->address }}');
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
