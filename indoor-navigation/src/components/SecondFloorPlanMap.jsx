import { useEffect, useRef, useState } from 'react';
// import loadDatabase from '../db/convertSQlite';
import { getFloorPlan, getRoute, getWalkways, getCompanies } from '../db/convertSQlite';
import * as L from 'leaflet';
import PathFinder from "geojson-path-finder";
import networkData from "../network/network.json";
import { useParams } from 'react-router-dom';
import { antPath } from 'leaflet-ant-path';
import 'leaflet.polyline.snakeanim';
function highlightFeature(e) {
    var layer = e.target;

    layer.setStyle({
        weight: 8,
        color: '#666',
        dashArray: '',
        fillOpacity: 0.7
    });


}

function resetHighlight(e) {
    var layer = e.target;
    let style = setFeatureStyle(layer.feature);
    layer.setStyle(style);
}

function setCompanyFeatureStyle(feature, latlng) {
    return L.circleMarker(latlng, {
        radius: 3,
        fillColor: "#ff7800",
        color: "#000",
        weight: 1,
        opacity: 0.,
        fillOpacity: 0.
    });
}
function setFeatureStyle(feature) {
    if (feature.properties.category == "Transistion") {
        return {
            fillColor: '#F6F1EE',
            weight: 2,
            opacity: 1,
            color: "white",
            opacity: 1,
            // dashArray: '3',
            fillOpacity: 0.8
        }
    } else {
        return {
            fillColor: '#A0153E',
            weight: 2,
            opacity: 1,
            color: "white",
            opacity: 1,
            // dashArray: '3',
            fillOpacity: 0.5
        }
    }
}
function SecondMap() {
    const { STORENAME } = useParams();
    const [lots, setLots] = useState([]);
    const [companies, setCompanies] = useState([]);
    const [routeToCompany, setRouteToCompany] = useState([]);
    const [walkWays, setWalkWays] = useState([]);
    const mapContainerRef = useRef(null);
    const startPoint = {
        "type": "Feature",
        "geometry": {
            "type": "Point",
            "coordinates": [-0.21852951300000001, 51.50455811900000214]
        },
        "properties": {}
    };

    const pathGeojson = {
        "type": "FeatureCollection",
        "features": []
    };

    useEffect(() => {
        const fetchData = async () => {
            try {
                const floorPlan = await getFloorPlan();
                const companiesData = await getCompanies();
                const routeToCompany = await getRoute(STORENAME);
                const walkways = await getWalkways();
                setWalkWays(walkways);
                console.log("lOTS" + floorPlan);
                setLots(floorPlan);
                setCompanies(companiesData);
                setRouteToCompany(routeToCompany);
                console.log('fetch WalK wAYS', walkways)
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        };

        fetchData();
    }, [STORENAME]);

    useEffect(() => {
        const mapContainer = mapContainerRef.current;
        let mapInstance;
        let lotsLabel = [];

        const clearMap = () => {
            if (mapInstance) {
                mapInstance.remove();
            }
        };

        if (!mapContainer) {
            console.error('Map container not found. Please ensure it exists in the DOM.');
            clearMap();
            return;
        }

        if (lots.length === 0) {
            console.log('No companies data available.');
            clearMap();
            return;
        }

        if (!mapInstance) {
            mapInstance = L.map(mapContainer).setView([51.507607, -0.221504], 18);


            mapInstance.on('zoomend', function () {
                // Check the current zoom level
                var currentZoom = mapInstance.getZoom();

                // Show or hide the marker based on the zoom level
                if (currentZoom >= 19) {
                    lotsLabel.forEach(function (marker) {
                        marker.addTo(mapInstance);
                    });

                } else {
                    lotsLabel.forEach(function (marker) {
                        mapInstance.removeLayer(marker);
                    });
                }
            });
            // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            //     attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            // }).addTo(mapInstance);

            // Add the marker to the map
            // L.marker([51.508254, -0.222253]).addTo(mapInstance);
            // L.marker([51.50455811900000214, -0.21852951300000001]).addTo(mapInstance);
            // Remove the zoom control
            mapInstance.zoomControl.remove();
        }
        const geojsonWalkways = {
            type: 'FeatureCollection',
            features: walkWays
                .filter(walkWay => walkWay.geojson)
                .map((walkWay) => ({
                    type: 'Feature',
                    geometry: walkWay.geojson,
                    properties: {}
                }))
        };
        const linesGeoJsonLayer = L.geoJSON(geojsonWalkways, {
            style: {
                color: 'purple',
                weight: 2
            }
        });//.addTo(mapInstance);

        const geojsonList = {
            type: 'FeatureCollection',
            features: lots
                .filter(lot => lot.geojson)
                .map((lot) => ({
                    type: 'Feature',
                    geometry: lot.geojson,
                    properties: {
                        levelcode: lot.levelcode,
                        category: lot.category,
                        storename: lot.storename
                    }
                }))
        };

        const companiesGeojson = {
            type: 'FeatureCollection',
            features: companies
                .filter(company => company.geojson)
                .map((company) => ({
                    type: 'Feature',
                    geometry: company.geojson,
                    properties: {
                        name: company.name
                    }
                }))
        };

        if (geojsonList.features.length > 0) {
            const lotsGeoJsonLayer = L.geoJSON(geojsonList, {
                style: setFeatureStyle,
                onEachFeature: function (feature, layer) {
                    layer.on({
                        mouseover: highlightFeature,
                        mouseout: resetHighlight
                    });
                    var label = L.marker(layer.getBounds().getCenter(), {
                        icon: L.divIcon({
                            className: 'label',
                            html: feature.properties.storename,
                            iconSize: [100, 40]
                        })
                    });
                    lotsLabel.push(label);
                }
            }).addTo(mapInstance);

            // const coordinatesArray = routeToCompany[0].Geometry.coordinates;
            // const latLngs = [];

            // for (let i = 0; i < coordinatesArray.length; i++) {
            //     const latLng = L.latLng(coordinatesArray[i][1], coordinatesArray[i][0]);
            //     latLngs.push(latLng);
            // }

            // const path = L.polyline(latLngs, {
            //     delay: 400,
            //     weight: 5,
            //     color: '#0000FF',
            //     pulseColor: '#FFFFFF',
            //     paused: false,
            //     reverse: false,
            //     hardwareAccelerated: true,
            //     snakingSpeed: 200,
            //     snakingPause: 200,
            // }).addTo(mapInstance);

            // mapInstance.addLayer(path);
            // path.snakeIn();


            const layerBounds = lotsGeoJsonLayer.getBounds();
            mapInstance.setMinZoom(mapInstance.getBoundsZoom(lotsGeoJsonLayer.getBounds()));
            mapInstance.setMaxBounds(layerBounds);
            mapInstance.on('drag', function () {
                mapInstance.panInsideBounds(layerBounds, { animate: false });
            });

        } else {
            console.error('No valid GeoJSON features in the companies array.');
            clearMap();
        }

        return clearMap;
    }, [lots, routeToCompany, walkWays]);


    return (
        <>
            <div ref={mapContainerRef} style={{ height: '100vh' }} />
        </>
    );
}

export default SecondMap;
