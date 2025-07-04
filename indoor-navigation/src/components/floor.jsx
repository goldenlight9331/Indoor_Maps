import { useEffect, useRef, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";

// import { faCircle, faSquareParking, faTaxi, faUserTie, faElevator, faChildReaching, faStairs, faSignInAlt, faMoneyBillAlt, faToilet, faRestroom, faSignOutAlt, faStar, faInfoCircle, faBatteryFull, faPoundSign } from "@fortawesome/free-solid-svg-icons";
// import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
// import { renderToStaticMarkup } from "react-dom/server";

import {
  getLevels,
  getCategory,
  getCompanies,
  getPOI,
  getFloorPlan,
  getAds,
  getPromotions,
  getBanners,
  getColors,
  getMisc,
  getRouteToAnotherFloor,
  initializeDatabaseIfNeeded,
  getKiosk,
  getSettings
} from "../db/convertSQlite";

import Slider from "./Slider";
import MapSearchBar from "./MapSearchBar";
import MapCategory from "./MapCategory";
import MyMapCheck from "./FloorMapCheck";
import MyMapCheckTwo from "./FloorMapCheckTwo";
import Clock from './Clock'
import FilteredStores from './FilteredStores'
import OptionsContainer from "./OptionsContainer";

import "./CSS/floor.css";

import * as L from "leaflet";

import "leaflet.polyline.snakeanim";
import "leaflet-polylinedecorator";
import "leaflet.marker.slideto";

import Modal from "react-bootstrap/Modal";

import Stairs from "./Icons/Stairs.png";
import Elevator from "./Icons/elevator.png";
import Escalator from "./Icons/Escalator.png";


export default function Floor() {
  const navigate = useNavigate();

  const { STORENAME, LEVELID } = useParams();
  const Kiosks = localStorage.getItem("selectedKiosk");

  const [mallName, setMallName] = useState('');
  const [currentRotation, setCurrentRotation] = useState(0);

  const [companies, setCompanies] = useState([]);
  const [companyInfo, setCompanyInfo] = useState([]);

  
  const [companyShow, setCompanyShow] = useState(false);
  
  const handleCloseModel = () => setCompanyShow(false);
  
  const handleOpenModel = (feature) => {

    if (feature.properties.storename) {

      if (companies.length > 0) {

        const matchingCompanies = companies.filter(company => company.storename === feature.properties.storename);

        if (matchingCompanies.length > 0) {
          setCompanyInfo(matchingCompanies[0]);
        }
        
        setCompanyShow(true);
      }
    }
  };


  const [mapVisible, setMapVisible] = useState(false);
  const [wasSecondMapClosed, setWasSecondMapClosed] = useState(false);

  const mainMapContainerRef = useRef(null);
  const mainMapRef = useRef(null);

  const labelRef = useRef(null);

  const [kiosksData, setkiosksData] = useState([]);
  const [lots, setLots] = useState([]);

  const userMarkerRef = useRef(null);
  const lotsLayerRef = useRef(null);
  const lotsLabelRef = useRef([]);
  const pathLayerRef = useRef(null);

  const [poi, setPoi] = useState([]);
  const [storePoi, setStorePoi] = useState("");
  const poiLayerRef = useRef(null);
  
    
  const [levels, setLevels] = useState([]);

  const secondMapContainerRef = useRef(null);
  const secondMapRef = useRef(null);
  const [secondLots, setSecondLots] = useState([]);
  const secondLotsLayerRef = useRef(null);
  const secondLotsLabelRef = useRef([]);
  const secondMapPathLayerRef = useRef(null);

  const [routeToAnotherFloor, setRouteToAnotherFloor] = useState([]);

  //type of access point for reaching another floor
  const [pathway, setPathway] = useState(null);
  
  //pop up for selecting pathway
  const [showPopUp, setShowPopUp] = useState(false);
  const handleClose = () => setShowPopUp(false);
  const handleShow = () => setShowPopUp(true);

  
  const [searchTerm, setSearchTerm] = useState("");
  const [suggestions, setSuggestions] = useState([]);

  const [activeButton, setActiveButton] = useState(null);
  const [slideIndex, setSlideIndex] = useState(null);


  const [ads, setAds] = useState([]);
  const [promotions, setPromotions] = useState([]);
  const [banners, setBanners] = useState([]);

  const [misc, setMisc] = useState([]);
  const [themes, setThemes] = useState([]);

  
  const [isDrawerOpen, setIsDrawerOpen] = useState(false);
  const drawerRef = useRef(null);


  const [categoryDropdown, setCategoryDropdown] = useState(false);
  const categoryDropdownRef = useRef(null);
  const categoryButtonRef = useRef(null);

  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [filteredCompanies, setFilteredCompanies] = useState([]);

  const [loading, setLoading] = useState(true);


  const buttonWidthPercent = 100 / 7;
  const mediumToUse = pathway;
  const targetFloorNumber = LEVELID;
  
  
  const useScreenOrientation = () => {
    const getOrientation = () => {
      const isLandscape = window.innerWidth > window.innerHeight;
      return isLandscape ? 'landscape-primary' : 'portrait-primary';
    };
  
    const [orientation, setOrientation] = useState(getOrientation());
  
    useEffect(() => {
      const handleOrientationChange = () => {
        setOrientation(getOrientation());
      };
  
      window.addEventListener('orientationchange', handleOrientationChange);
      window.addEventListener('resize', handleOrientationChange);
      
      return () => {
        window.removeEventListener('orientationchange', handleOrientationChange);
        window.removeEventListener('resize', handleOrientationChange);
      };
    }, []);
  
    return orientation;
  };  

  const orientation = useScreenOrientation();


  let timeout;

  const resetTimer = () => {
    if (timeout) clearTimeout(timeout);

    //default is one minute
    let duration = 60000;

    if (misc.length > 0) {
      //seconds to microseconds
      duration = misc[0].screensaver_time * 1000;
    }

    timeout = setTimeout(() => {
      navigate('/');
    }, duration);
  };

  useEffect(() => {
    // Set up the initial timer
    resetTimer();

    // Listen for user activity events
    window.addEventListener('mousemove', resetTimer);
    window.addEventListener('keydown', resetTimer);
    window.addEventListener('touchstart', resetTimer);

    // Cleanup on component unmount
    return () => {
      clearTimeout(timeout);
      window.removeEventListener('mousemove', resetTimer);
      window.removeEventListener('keydown', resetTimer);
      window.removeEventListener('touchstart', resetTimer);
    };
  }, [misc]);


  
  useEffect(() => {
    const fetchData = async () => {
      try {
        await initializeDatabaseIfNeeded();

        const fetchedSettings = await getSettings();
        
        const fetchKiosks = await getKiosk(Kiosks);
        const baseFloorNumber = fetchKiosks[0].levelid;

        const fetchedCategories = await getCategory();

        const fetchedPoi = await getPOI(activeButton ?? baseFloorNumber);
        const fetchCompanies = await getCompanies();

        const floorPlanLevels = await getFloorPlan(activeButton ?? baseFloorNumber);

        const fetchAds = await getAds();
        const fetchPromotions = await getPromotions();
        const fetchBanners = await getBanners();

        const fetchedMisc = await getMisc();
        const fetchColors = await getColors();

        const fetchLevels = await getLevels();
        const secondLots = await getFloorPlan(LEVELID);
        
        const routeToAnotherFloor = await getRouteToAnotherFloor(
          baseFloorNumber,
          mediumToUse,
          targetFloorNumber,
          STORENAME,
          [fetchKiosks[0].Latitude, fetchKiosks[0].Longitude]
        );
        setRouteToAnotherFloor(routeToAnotherFloor);

        setMallName(fetchedSettings[0].name);

        setkiosksData(fetchKiosks[0]);
        if(fetchedSettings[0].nor_ang){
          setCurrentRotation(fetchedSettings[0].nor_ang);
        }
        
        if (activeButton === null) {
          setActiveButton(baseFloorNumber);
          setSlideIndex(baseFloorNumber);
        }
        
        setLevels(fetchLevels);
        setCategories(fetchedCategories);
        setPoi(fetchedPoi);
        setCompanies(fetchCompanies);
        setLots(floorPlanLevels);

        setSecondLots(secondLots);

        setAds(fetchAds);
        setPromotions(fetchPromotions);
        setBanners(fetchBanners);

        setThemes(fetchColors);
        setMisc(fetchedMisc);

        if (LEVELID != baseFloorNumber) {
          if (mapVisible === false) {
            handleShow();
          }
        }
      }
      catch (error) {
        console.error("Error fetching data:", error);
      }
      finally {
        setLoading(false);
      }
    };

    fetchData();

    if (mapVisible) {
      fetchData();
    }
    else if (LEVELID == undefined) {
      fetchData();
    }

  }, [STORENAME, LEVELID, mapVisible, activeButton, Kiosks]);


  const kioskPoint = [kiosksData.Latitude, kiosksData.Longitude];


  //Helper functions for Map Initialization and Features

  function initializeMap(container) {
    
    let map;
    if (kiosksData) {
      map = L.map(container,{
				zoomAnimation: false, /* DEBUG: L.Renderer._updateTransform() */
				rotate: true,
				rotateControl: {
					closeOnZeroBearing: false,
				},
				bearing: currentRotation,
				compassBearing: true,
				touchRotate: true,
			}).setView([kiosksData.Latitude, kiosksData.Longitude], 18);
    }
    else {
      map = L.map(container,{
				// zoomAnimation: false, /* DEBUG: L.Renderer._updateTransform() */
				// rotate: true,
				rotateControl: {
					closeOnZeroBearing: false,
				},
				bearing: currentRotation,
				compassBearing: false,
				// touchRotate: true,
			}).setView([51.5059769463243, -0.218707120059119], 18);
    }
    
    map.zoomControl.remove();


    L.control.zoom({
      position: 'topleft' //positions: 'topleft', 'topright', 'bottomleft', 'bottomright'
    }).addTo(map);


    setTimeout(function(){
      map.setBearing(currentRotation);
    },200);


    let lastBearing = map.getBearing();

    // Save bearing before tab hides
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        lastBearing = map.getBearing();
      } else {
        // Delay restore to allow map to redraw
        setTimeout(() => {
          if (map && map.setBearing) {
            map.setBearing(lastBearing);
          }
        }, 100); // slight delay to avoid timing issues
      }
    });
    return map;
  }

  function handleZoom(map, labelRef) {
    const currentZoom = map.getZoom();
    labelRef.current.forEach(labelObj => {
      if (currentZoom >= 19) {
        if (!labelObj.visible) {
          labelObj.marker.addTo(map);
          labelObj.visible = true;
        }
      } else {
        if (labelObj.visible) {
          map.removeLayer(labelObj.marker);
          labelObj.visible = false;
        }
      }
    });
  }


  function setFeatureStyle(feature) {
    if (feature.properties.category != null) {
      return {
        fillColor: "#FFFFFF",
        weight: 2,
        opacity: 1,
        color: "white",
        fillOpacity: 0.8,
      };
    }
    else {
      return {
        fillColor: themes[0].lots_color ? themes[0].lots_color : "#A0153E",
        weight: 2,
        opacity: 1,
        color: "white",
        fillOpacity: 0.5,
      };
    }
  }

   function setLotsFeatureStyle(feature) {
    let lotColor = themes[0].lots_color ? themes[0].lots_color : "#A0153E";
    if (feature.properties.category != null) {
      lotColor = "#FFFFFF";
    }

    if (feature.properties.color != null) {
      lotColor = feature.properties.color;
    }

    return {
        fillColor: lotColor,
        weight: 2,
        opacity: 1,
        color: "white",
        fillOpacity: 0.5,
      };
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
    let style = setLotsFeatureStyle(layer.feature);
    layer.setStyle(style);
  }

  function createLotsGeoJsonLayer(lots) {

    const mainMapLotsGeojson = {
      type: "FeatureCollection",
      features: lots
        .filter(lot => lot.geojson)
        .map(lot => ({
          type: "Feature",
          geometry: lot.geojson,
          properties: {
            levelcode: lot.levelcode,
            category: lot.category,
            storename: lot.storename,
            color: lot.color,
          },
        }))
      };

    if (mainMapLotsGeojson.features.length > 0)
    {
      return L.geoJSON(mainMapLotsGeojson, {
        style: setLotsFeatureStyle,
        onEachFeature: (feature, layer) => {
          layer.on({
            mouseover: highlightFeature,
            mouseout: resetHighlight,
            click: (() => handleOpenModel(feature))
          });
          if (feature.properties && feature.properties.storename) {
            layer.bindTooltip(
                `<span style="font-weight: bold;">${feature.properties.storename}</span>`, 
                { sticky: true }
            );
          }
        }
      });
    }

    return null;
  }

  function setupMapBounds(map, layer) {
    const layerBounds = layer.getBounds();

    map.setMinZoom(map.getBoundsZoom(layerBounds));
    
    map.setMaxBounds(layerBounds);
    
    map.on("drag", () => {
      map.panInsideBounds(layerBounds, { animate: false });
    });
  }

  function getLatLngArray(coordinatesArray) {
    const latLngs = [];

    for (let i = 0; i < coordinatesArray.length; i++) {
      const lat = coordinatesArray[i][1];
      const lng = coordinatesArray[i][0];
      if (lat && lng) {
        const latLng = L.latLng(lat, lng);
        latLngs.push(latLng);
      } else {
        console.error("Invalid coordinates:", coordinatesArray[i]);
      }
    }
    return latLngs;
  }

  function drawPathOnMap(map, kioskPoint, routeData) {
    // Remove old path line if it exists
    if (pathLayerRef.current) {
      map.removeLayer(pathLayerRef.current);
      map.removeLayer(pathLayerRef.current_decorator);
      map.removeLayer(pathLayerRef.jumpingMarker);
    }
  
    if (!routeData || !routeData["routing_data"]) {
      console.log("Route data or routing data is missing.");
      return;
    }
  
    const isMultifloor = routeData["routing_type"] === "multifloor";
    let lineLatLng;
  
    try {
      lineLatLng = getLatLngArray(routeData["routing_data"][0][0].Geometry.coordinates);
    }
    catch (error) {
      console.error("Error extracting line coordinates:", error);
      return;
    }
  
    if (lineLatLng.length > 0) {
      lineLatLng.unshift(L.latLng(kioskPoint[0], kioskPoint[1]));
  
      if (isMultifloor) {
        try {
          const poiCoordinates = routeData["routing_data"][2][0].poigeojson.coordinates;
          lineLatLng.push(L.latLng(poiCoordinates[1], poiCoordinates[0]));
        } catch (error) {
          console.error("Error extracting POI coordinates:", error);
          return;
        }
      } else {
        try {
          const companyCoordinates = routeData["routing_data"][1][0].companygeojson.coordinates;
          lineLatLng.push(L.latLng(companyCoordinates[1], companyCoordinates[0]));
        } catch (error) {
          console.error("Error extracting company coordinates:", error);
          return;
        }
      }
  
      const path = L.polyline(lineLatLng, {
        delay: 400,
        weight: 5,
        color: "#0000FF",
        pulseColor: "#FFFFFF",
        paused: false,
        reverse: false,
        hardwareAccelerated: true,
        snakingSpeed: 200,
        snakingPause: 100,
        dashArray: '10, 10' 
      }).addTo(map);
      map.fitBounds(path.getBounds());

      const pathDecorator = L.polylineDecorator(path, {
          patterns: [
              {offset: 25, repeat: 50, symbol: L.Symbol.arrowHead({pixelSize: 15, pathOptions: {fillOpacity: 1, weight: 0,color: '#ff0000', fillColor: '#ff0000'}})}
          ]
      }).addTo(map);
      path.snakeIn();
      path.on('snakeend', function() {
          setTimeout(() => {
            path.snakeIn();
          }, 1000); // Optional delay between loops (in milliseconds)
      });
  
      //  the new path layer reference for future removal

      var jumpingManIcon = L.divIcon({
            className: 'emoji-icon', // Custom class for styling
            html: '<span style="font-size: 32px; text-align: center;">🚶‍♂️</span>', // Jumping man emoji
            iconSize: [32, 32], // Adjust size as needed
            iconAnchor: [16, 32], // Anchor the icon appropriately
      });

      const movingMarker = L.marker([kioskPoint[0], kioskPoint[1]], { icon: jumpingManIcon }).addTo(map);
      let currentPointIndex = 0;
      const moveMarker = () => {
          currentPointIndex++;
          if (currentPointIndex >= lineLatLng.length) {
            currentPointIndex = 0; // Reset the movement after completing the polyline
          }

          // Move the marker smoothly to the next point
          movingMarker.slideTo(lineLatLng[currentPointIndex], {
            duration: 200, // Animation speed in milliseconds (adjust as needed)
          });

          

          // Set a timeout to keep the marker moving through the polyline
          setTimeout(moveMarker, 400); // Adjust this delay to control movement frequency
        };

        // Start the marker movement
        // moveMarker();

        pathLayerRef.current = path;
        pathLayerRef.current_decorator = pathDecorator;
        pathLayerRef.jumpingMarker = movingMarker;
    }
    else {
      //console.error("No valid coordinates to draw the path.");
    }
  }
  
  //mainMap useEffect
  useEffect(() => {
    const mainMapContainer = mainMapContainerRef.current;
  
    if (!mainMapContainer) {
      console.error("Main Map container reference is not available.");
      return;
    }
  
    let mainMap = mainMapRef.current;

    //re-render main map when second map is visible
    if (mapVisible) {
      if (mainMap) {
        if (labelRef.current) {
          mainMap.removeControl(labelRef.current);
          labelRef.current = null;
        }
        
        mainMap.remove();
        mainMap.off();
        mainMap.eachLayer(layer => mainMap.removeLayer(layer));
        mainMapRef.current = null;
        mainMap = null;
      }
    }

    //re-render the main map once when second map is closed
    if (wasSecondMapClosed) {
      if (mainMap) {
        if (labelRef.current) {
          mainMap.removeControl(labelRef.current);
          labelRef.current = null;
        }  

        mainMap.remove();
        mainMap.off();
        mainMap.eachLayer(layer => mainMap.removeLayer(layer));  
        mainMapRef.current = null;
        mainMap = null;

        //resetting state
        setWasSecondMapClosed(false);
      }
    }

    if (!mainMap) {
      mainMap = initializeMap(mainMapContainer);
      console.log('initialised MAIN map');
      mainMapRef.current = mainMap;
    }

    if (labelRef.current) {
      mainMap.removeControl(labelRef.current);
      labelRef.current = null;
    }

    const LabelControl = L.Control.extend({
      options: {
        position: 'topright',
      },
      onAdd: function () {
        const labelDiv = L.DomUtil.create('div', 'map-label');
        labelDiv.innerHTML = `<h1><strong>Floor ${activeButton}</strong></h1>`;
        labelDiv.style.padding = '5px';
        labelDiv.style.borderRadius = '5px';
        return labelDiv;
      },
    });

    // Create and store the label control in the ref
    const labelControlInstance = new LabelControl();
    mainMap.addControl(labelControlInstance);
    labelRef.current = labelControlInstance;
  
    if (lotsLayerRef.current) {
      mainMap.removeLayer(lotsLayerRef.current);
      lotsLayerRef.current = null;
    }
  
    lotsLabelRef.current.forEach(labelObj => mainMap.removeLayer(labelObj.marker));
    lotsLabelRef.current = [];
  
    if (kioskPoint && kioskPoint[0] && kioskPoint[1]) {
      if (kiosksData.levelid === activeButton) {
        if (!userMarkerRef.current) {
          const jumpingManIcon = L.divIcon({
            className: 'emoji-icon', // Custom class for stylingz
            html: '<span style="font-size: 32px; text-align: center;">🚶‍♂️</span>', // Jumping man emoji
            iconSize: [32, 32], // Adjust size as needed
            iconAnchor: [16, 32], // Anchor the icon appropriately
          });

          // Create the marker using the custom icon
          const marker = L.marker([kioskPoint[0], kioskPoint[1]], { icon: jumpingManIcon }).addTo(mainMap);
          const popup = L.popup({
            offset: L.point(0, -15) // Adjust the second parameter for vertical offset (negative for upward)
          })
          .setContent("<b>You are here</b>") // Set the content of the popup

          // Bind the popup to the marker and open it
          marker.bindPopup(popup).openPopup();

          // Store marker reference
          userMarkerRef.current = marker;

          console.log('MAIN', mainMap._layers, userMarkerRef.current);
        }
      }
      else {
        //remove marker if the level is different
        if (userMarkerRef.current) {
          mainMap.removeLayer(userMarkerRef.current);
          userMarkerRef.current = null;
        }
      }
    }
    else {
      console.error("Invalid kiosk coordinates:", kioskPoint);
    }
  
    const lotsGeoJsonLayer = createLotsGeoJsonLayer(lots);
  
    if (lotsGeoJsonLayer) {
      lotsGeoJsonLayer.addTo(mainMap);
      lotsLayerRef.current = lotsGeoJsonLayer;
      setupMapBounds(mainMap, lotsGeoJsonLayer);
  
      lotsGeoJsonLayer.eachLayer(layer => {
        const feature = layer.feature;
        const label = L.marker(layer.getBounds().getCenter(), {
          icon: L.divIcon({
            className: "label",
            html: `<span style="font-weight: bold; color: #001c73;">${feature.properties.storename}</span>`,
            iconSize: [100, 40], // Adjust size as needed
            iconAnchor: [50, 20] // Center the icon
          }),
        });
  
        lotsLabelRef.current.push({ marker: label, visible: false });
      });
  
      handleZoom(mainMap, lotsLabelRef);
      mainMap.on("zoomend", () => handleZoom(mainMap, lotsLabelRef));
  
      if (routeToAnotherFloor) {
        drawPathOnMap(mainMap, kioskPoint, routeToAnotherFloor);
      }
      else {
        console.error("No valid routing data to draw the path.");
      }
    }
    else {
      console.error("No valid GeoJSON features in the lots array.");
    }
  
  }, [lots, routeToAnotherFloor, mallName, activeButton]);
  
  //secondMap useEffect
  useEffect(() => {
    
    const secondMapContainer = secondMapContainerRef.current;

    if (!secondMapContainer) {
      console.log("Second Map container reference is not available.");

      return;
    }

    let secondMap = secondMapRef.current;

    // Initialize new map instance if it doesn't exist
    if (!secondMap) {
      secondMap = initializeMap(secondMapContainer);
      console.log('Initialised SECOND map');
      secondMapRef.current = secondMap;
    }

    const LabelControl = L.Control.extend({
      options: {
        position: 'topright',
      },
      onAdd: function () {
        const labelDiv = L.DomUtil.create('div', 'map-label');
        labelDiv.innerHTML = `<h1><strong>Floor ${LEVELID}</strong></h1>`;
        labelDiv.style.padding = '5px';
        labelDiv.style.borderRadius = '5px';
        return labelDiv;
      },
    });

    // Create and store the label control in the ref
    const labelControlInstance = new LabelControl();
    secondMap.addControl(labelControlInstance);


    // Remove existing lots layer if it exists
    if (secondLotsLayerRef.current) {
      secondMap.removeLayer(secondLotsLayerRef.current);
    }

    // Remove existing labels
    secondLotsLabelRef.current.forEach(labelObj => secondMap.removeLayer(labelObj.marker));
    secondLotsLabelRef.current = []; // Clear the reference array

    // Add lots GeoJSON layer for the second map
    const secondLotsGeoJsonLayer = createLotsGeoJsonLayer(secondLots);

    if (secondLotsGeoJsonLayer) {
        secondLotsGeoJsonLayer.addTo(secondMap);
        secondLotsLayerRef.current = secondLotsGeoJsonLayer;
        setupMapBounds(secondMap, secondLotsGeoJsonLayer);

        // Add new labels and manage their visibility based on zoom level
        secondLotsGeoJsonLayer.eachLayer(layer => {
            const feature = layer.feature;
            const label = L.marker(layer.getBounds().getCenter(), {
                icon: L.divIcon({
                    className: "label",
                    html: feature.properties.storename,
                    iconSize: [100, 40],
                }),
            });

            secondLotsLabelRef.current.push({ marker: label, visible: false });
        });

        // Initialize zoom-based label visibility
        handleZoom(secondMap, secondLotsLabelRef);

        // Attach zoomend event to update labels on zoom
        secondMap.on("zoomend", () => handleZoom(secondMap, secondLotsLabelRef));

        // Draw path on second map
        if (routeToAnotherFloor && routeToAnotherFloor["routing_data"] && routeToAnotherFloor["routing_data"][1] && routeToAnotherFloor["routing_data"][3]) {

            const lineLatLngTargetFloor = getLatLngArray(routeToAnotherFloor["routing_data"][1][0].Geometry.coordinates);

            if (lineLatLngTargetFloor.length > 0) {
                const companyCoords = routeToAnotherFloor["routing_data"][3][0].companygeojson?.coordinates;
                
                if (companyCoords) {
                    lineLatLngTargetFloor.push(L.latLng(companyCoords[1], companyCoords[0]));

                    const path = L.polyline(lineLatLngTargetFloor, {
                      delay: 400,
                      weight: 5,
                      color: "#0000FF",
                      pulseColor: "#FFFFFF",
                      paused: false,
                      reverse: false,
                      hardwareAccelerated: true,
                      snakingSpeed: 200,
                      snakingPause: 100,
                      dashArray: '10, 10' 
                    }).addTo(secondMap);
                    secondMap.fitBounds(path.getBounds());

                    const pathDecorator = L.polylineDecorator(path, {
                      patterns: [
                            {offset: 25, repeat: 50, symbol: L.Symbol.arrowHead({pixelSize: 15, pathOptions: {fillOpacity: 1, weight: 0,color: '#ff0000', fillColor: '#ff0000'}})}
                        ]
                    }).addTo(secondMap);
                    path.snakeIn();
                    path.on('snakeend', function() {
                        setTimeout(() => {
                          path.snakeIn();
                        }, 1000); // Optional delay between loops (in milliseconds)
                    });

                    // Save the path layer reference
                    secondMapPathLayerRef.current = path;
                }
                else {
                  console.error("Company coordinates are not available.");
                }
            }
        }
        else
        {
          if (routeToAnotherFloor && routeToAnotherFloor["routing_type"] == "singlefloor") {
            
            // Clean up function to remove layers and labels
            if (secondLotsLayerRef.current) {
              secondMap.removeLayer(secondLotsLayerRef.current);
            }
            secondLotsLabelRef.current.forEach(labelObj => secondMap.removeLayer(labelObj.marker));
            secondLotsLabelRef.current = [];

            if (secondMapPathLayerRef.current) {
              secondMap.removeLayer(secondMapPathLayerRef.current);
            }

            secondMapPathLayerRef.current = null;            

            // Remove zoomend event listener
            secondMap.off("zoomend", () => handleZoom(secondMap, secondLotsLabelRef));
              
            // Remove the map instance
            if (secondMap) {
              secondMap.remove();
              secondMap.off();
              secondMap.eachLayer(layer => {
                secondMap.removeLayer(layer);
              });
              secondMap = null;

              secondMapRef.current = null;
              
              setMapVisible(false);

              setWasSecondMapClosed(true);

              return;
            }
          }
          else
          {
            console.log("Route data is missing or incomplete.");
          }
        }
    }
    else {
      console.error("No valid GeoJSON features in the second lots array.");
    }

    return () => {
        // Clean up function to remove layers and labels
        if (secondLotsLayerRef.current) {
          secondMap.removeLayer(secondLotsLayerRef.current);
        }
        secondLotsLabelRef.current.forEach(labelObj => secondMap.removeLayer(labelObj.marker));
        secondLotsLabelRef.current = []; // Clear the reference array

        // Remove the map instance
        if (secondMap) {
          // Remove zoomend event listener
          secondMap.off("zoomend", () => handleZoom(secondMap, secondLotsLabelRef));

          secondMap.remove();
          secondMap.off();
          secondMap.eachLayer(layer => {
            secondMap.removeLayer(layer);
          });
          secondMap = null;

          secondMapRef.current = null;
        }

        setWasSecondMapClosed(true);
    };
  }, [secondLots, routeToAnotherFloor, secondMapContainerRef]);



  //Helper functions for poi management

  const handleClick = (poiName) => {
    setStorePoi(poiName);
  };

  const handleClearPoi = () => {
    setStorePoi("");
  };

  // const iconMap = {
  //   Escalator: faStairs,
  //   Elevator: faElevator,
  //   Stairs: faStairs,
  //   Enter: faSignInAlt,
  //   ATM: faMoneyBillAlt,
  //   Toilet: faToilet,
  //   WC: faToilet,
  //   Restroom: faRestroom,
  //   Exit: faSignOutAlt,
  //   Favourite: faStar,
  //   Info: faInfoCircle,
  //   "Mobile Charger": faBatteryFull,
  //   "Kid Slide": faChildReaching,
  //   "Pound Sterling": faPoundSign,
  //   "Car Parking": faSquareParking,
  //   "Car Park": faSquareParking,
  //   "Cab Service": faTaxi,
  //   "Board Person": faUserTie
  // };

  // // Function to generate a Data URL from a FontAwesome icon
  // const getIconDataUrl = (faIcon) => {
  //   const svgMarkup = renderToStaticMarkup(
  //     <FontAwesomeIcon icon={faIcon} size="2x" color={(themes.length > 0) && themes[0].button_color}/>
  //   );
  //   const svgBlob = new Blob([svgMarkup], { type: "image/svg+xml" });
  //   const url = URL.createObjectURL(svgBlob);
  //   return url;
  // };
  
  useEffect(() => {
    const mainMap = mainMapRef.current;
  
    if (!mainMap) {
      console.error("Main map is not initialized.");
      return;
    }
  
    // Create POI layer and add it to the map
    const geojsonPoi = {
      type: "FeatureCollection",
      features: poi
        .filter((poigeo) => poigeo.geojson || poigeo.geom)
        .map((poigeo) => ({
          type: "Feature",
          geometry: poigeo.geojson || poigeo.geom,
          properties: { poiname: poigeo.POINAME ? poigeo.POINAME : poigeo.poiname },
        })),
    };
  
    // Remove existing POI layer if it exists
    if (poiLayerRef.current) {
      mainMap.removeLayer(poiLayerRef.current);
    }

  
    // Create and add the new POI layer
    const newPoiLayer = L.geoJSON(geojsonPoi, {
      filter: function (feature) {
        if (storePoi && feature.properties.poiname === storePoi) {
          return true;
        }
        else if (!storePoi) {
          return true;
        }
        return false;
      },
      pointToLayer: function (feature, latlng) {
        // const faIcon = iconMap[feature.properties.poiname] || faInfoCircle; // Default to faInfoCircle if no match
        // const iconUrl = getIconDataUrl(faIcon);

        const customIcon = L.icon({
          iconUrl: `/poiImages/${feature.properties.poiname}.png`,
          iconSize: [40, 40],
          iconAnchor: [20, 40]
        });

        return L.marker(latlng, { icon: customIcon });
      },
    }).addTo(mainMap);
  
    // Update the ref to the current poiLayer
    poiLayerRef.current = newPoiLayer;
  
    return () => {
      // Cleanup function to remove the POI layer when the component unmounts or dependencies change
      if (mainMap && poiLayerRef.current) {
        mainMap.removeLayer(poiLayerRef.current);
      }
    };
  }, [poi, storePoi]);
  

  let bannerImageUrl = "";
  if (banners.length > 0) {
    bannerImageUrl = `data:image/jpeg;base64,${banners[0].image}`;
  }
  else {
    console.log("Banners list is empty");
  }

  let adImageUrl = "";
  if (ads.length > 0) {
    adImageUrl = `data:image/jpeg;base64,${ads[0].image}`;
  }
  else {
    console.log("Ads list is empty");
  }

  let promotionImageUrl = "";
  if (promotions.length > 0) {
    promotionImageUrl = `data:image/jpeg;base64,${promotions[0].image}`;
  }
  else {
    console.log("Promotions list is empty");
  }
  

  //Search and Suggestions Functions:

  const generateSuggestions = (searchTerm) => {
    if (!searchTerm) {
      return [];
    }
    return companies
      .filter((company) =>
        company.storename.toLowerCase().includes(searchTerm.toLowerCase())
      )
      .slice(0, 5);
  };

  const handleSearch = (event) => {
    const searchTermValue = event.target.value.toLowerCase();

    setSearchTerm(searchTermValue);
    
    setSuggestions(generateSuggestions(searchTermValue));
  };

  const navigateToDashboard = (storename, levelid) => {
    if (kiosksData) {
      setActiveButton(kiosksData.levelid);      
    }

    setMapVisible(false);

    navigate(`/floor/${storename}/${levelid}`);
  };

  const handleSuggestionClick = (suggestion) => {
    let existingData = null;
    try {
      existingData = JSON.parse(localStorage.getItem("user_search_data"));
    }
    catch (error) {
      console.error("Error parsing existing search data:", error);
    }
  
    const now = new Date();
    const formattedTimestamp = now.getFullYear() + '-' +
        String(now.getMonth() + 1).padStart(2, '0') + '-' +
        String(now.getDate()).padStart(2, '0') + ' ' +
        String(now.getHours()).padStart(2, '0') + ':' +
        String(now.getMinutes()).padStart(2, '0') + ':' +
        String(now.getSeconds()).padStart(2, '0');

    const suggestionData = {
      kiosk_id: kiosksData.id,
      store_id: suggestion.id,
      timestamp: formattedTimestamp
    };

    let updatedData;
    if (existingData) {
      updatedData = existingData;
      
      updatedData.suggestions = updatedData.suggestions || []; //ensuring array exists

      updatedData.suggestions.push(suggestionData);
    }
    else {
      // No existing data: Create a new object with the first suggestion
      updatedData = {
        suggestions: [suggestionData]
      };
    }
  
    try {
      localStorage.setItem("user_search_data", JSON.stringify(updatedData));
    }
    catch (error) {
      console.error("Error saving search data to localStorage:", error);
    }
  
    setSearchTerm(suggestion.storename);
    
    setSuggestions([]);
  
    navigateToDashboard(suggestion.storename, suggestion.levelid);
  };


  const handlePopupValue = (button) => {
    setPathway(button);
  };


  //Level Changing Functions:

  const handleSliderButtonClick = (level) => {
    setActiveButton(level.levelid);

    if (kiosksData) {
      navigate(`/floor/v/${kiosksData.levelid}`);
    }
  };

  const handleResetButtonClick = () => {
    setActiveButton(kiosksData.levelid);

    setSearchTerm('');
  }

  //Side Drawer, Category Selection, etc:

  const toggleDrawer = () => {
    setIsDrawerOpen(!isDrawerOpen);
  };

  //clicking outside side drawer
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (isDrawerOpen && drawerRef.current && !drawerRef.current.contains(event.target)) {
        toggleDrawer();
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, [isDrawerOpen]);


  const handleDropdownToggle = (event) => {
    event.preventDefault();
    event.stopPropagation();

    setCategoryDropdown((prevState) => !prevState);
  };
  
  // clicking outside category dropdown
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (
        categoryDropdownRef.current && !categoryDropdownRef.current.contains(event.target) &&
        categoryButtonRef.current && !categoryButtonRef.current.contains(event.target)
      ) {
        setCategoryDropdown(false);
      }
    };
  
    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [categoryDropdown]);
  
  

  const handleCategorySelection = (newCat) => {
    setSelectedCategory(newCat);

    setCategoryDropdown(false);
  }

  //filtering stores to display
  useEffect(() => {
    if (selectedCategory) {
      // Filter based on selected category
      var filteredCompanies = [];
      
      if (selectedCategory !== "all") {
        filteredCompanies = companies.filter(company => company.category === selectedCategory);        
      }
      else
      {
        filteredCompanies = companies;
      }
  
      //Further filter based on searchTerm
      if (searchTerm) {
        const filtered = filteredCompanies.filter(company =>
          company.storename.toLowerCase().includes(searchTerm.toLowerCase())
        );
        
        setFilteredCompanies(filtered);
      }
      else
      {
        setFilteredCompanies(filteredCompanies);
      }
    }
  }, [selectedCategory, searchTerm, companies]);

  if (loading) {
    return <h1>Loading...</h1>
  }

  return (
      <>
        <Modal show={companyShow} onHide={handleCloseModel}>
          <Modal.Body style={{wordWrap: 'break-word'}}>
            <button
              type="button"
              className="close"
              onClick={handleCloseModel}
              style={{
                position: 'absolute',
                top: '10px',
                right: '15px',
                fontSize: '20px',
                background: 'none',
                border: 'none',
                cursor: 'pointer',
              }}
            >
              &times;
            </button>

            <center>
              <img
                src={`data:image/png;base64,${companyInfo.image_data}`}
                alt="Store"
                style={{ maxWidth: '100%', height: 'auto' }}
              />
            </center>

            <br />

            <p>
              <b>Store Name:</b> {companyInfo.storename}
            </p>

            <p>
              <b>Floor Number:</b> {companyInfo.levelid}
            </p>

            <p>
              <b>Phone:</b> {companyInfo.phone}
            </p>

            <p>
              <b>Website:</b> {companyInfo.website}
            </p>

            <p>
              <b>Time:</b> {companyInfo.time}
            </p>

            <p>
              <b>Category:</b> {companyInfo.sub_cat}
            </p>

          </Modal.Body>
        </Modal>

        <Modal show={showPopUp} onHide={handleClose}>
          <Modal.Body style={{ backgroundColor: (themes.length > 0) ? themes[0].options_ribbon_color : "#A0153E" }}>
            <center>
              <b style={{ color: (themes.length > 0) ? themes[0].button_color : "#A0153E" }}>
                {searchTerm ? `${searchTerm} is on ${LEVELID} floor!. Choose pathway to go!` : `${STORENAME} is on ${LEVELID} floor!. Choose pathway to go!`}
              </b>

              <br></br>

              <div className="card mb-3" style={{ backgroundColor: (themes.length > 0) ? themes[0].options_ribbon_color : "#A0153E" }}>
                <div className="card-body">
                  <a
                    style={{ cursor: "pointer" }}
                    onClick={() => {
                      handleClose();
                      setMapVisible(true);
                      handlePopupValue("Stairs");
                    }}
                  >
                    <img src={Stairs} alt="Stairs" />{" "}
                  </a>

                  <a
                    style={{ cursor: "pointer" }}
                    onClick={() => {
                      handleClose();
                      setMapVisible(true);
                      handlePopupValue("Escalator");
                    }}
                  >
                    <img src={Escalator} alt="Escalator" />
                  </a>

                  <a
                    style={{ cursor: "pointer" }}
                    onClick={() => {
                      handleClose();
                      setMapVisible(true);
                      handlePopupValue("Elevator");
                    }}
                  >
                    <img src={Elevator} alt="Elevator" />{" "}
                  </a>
                </div>
              </div>
            </center>
          </Modal.Body>
        </Modal>


        {orientation.startsWith('landscape') ? (
            <div className="row g-0" style={{overflow: "hidden", height: '100vh'}}>

              <div className="col-9" style={{position: "relative"}}>
      
                <div className="row">
                  <img style={{width: '100%', height: '10vh', objectFit: 'cover'}} src={bannerImageUrl} alt="banner" />
                </div>

                <Slider
                    slideIndex={slideIndex}
                    buttonWidthPercent={buttonWidthPercent}
                    slides={levels}
                    activeButton={activeButton}
                    handleButtonClick={handleSliderButtonClick}
                  />

                <div className="map">

                  <div className="row">
      
                    <div className={`${!mapVisible ? "col-12" : "col-6"}`}>
                      <MyMapCheckTwo mainMapContainerRef={mainMapContainerRef} type={orientation} bgColor={(themes.length > 0) && themes[0].map_container_color} />
                    </div>
                    <div className="col-6">
                      {mapVisible && (
                        <MyMapCheck secondMapContainerRef={secondMapContainerRef} type={orientation} bgColor={(themes.length > 0) && themes[0].map_container_color}/>
                      )}
                    </div>
                  </div>
      
                </div>
                
                <div className="row g-0 w-100" style={{position: "absolute", bottom: 0}}>

                  <OptionsContainer baseFloor={kiosksData.levelid} poi={poi} handleClick={handleClick} handleClearPoi={handleClearPoi} handleResetButtonClick={handleResetButtonClick} buttonColor={(themes.length > 0) && themes[0].button_color} bgColor={(themes.length > 0) && themes[0].options_ribbon_color} />

                </div>

              </div>
      
              <div className="col-3" style={{height: '100vh'}}>

                {isDrawerOpen ? 
                  <div ref={drawerRef} className={`side-drawer ${isDrawerOpen ? 'open' : ''}`} >

                  {
                    (themes.length > 0) && <style>
                    {`
                      .side-drawer::-webkit-scrollbar-thumb {
                        background-color: ${themes[0].categories_background_color};
                      }
                    `}
                    </style>
                   }

                    <div className="top-container" style={{backgroundColor : (themes.length > 0) && themes[0].categories_background_color}}>
                      <MapSearchBar
                        handleSearch={handleSearch}
                        handleSuggestionClick={handleSuggestionClick}
                        suggestions={suggestions}
                        searchTerm={searchTerm}
                      />
        
                      <div className="container d-flex justify-content-center flex-row mt-2 mb-2">
                        <button
                          className="dropdown-toggle btn btn-select"
                          onClick={handleDropdownToggle}
                          ref={categoryButtonRef}
                        >
                          Categories
                        </button>
                      </div>

                      <div className="container d-flex justify-content-center" ref={categoryDropdownRef}>
                        {categoryDropdown && (
                          <MapCategory
                            categories={categories}
                            handleCategorySelection={handleCategorySelection}
                            selectedCategory={selectedCategory}
                          />
                        )}
                      </div>
                    </div>
      
                    <FilteredStores filteredCompanies={filteredCompanies} navigateToDashboard={navigateToDashboard} themeColor={(themes.length > 0) && themes[0].categories_background_color}/>
                  </div>
                : 
                  <div className="d-flex flex-column" style={{height: '100vh', alignItems: "space-between"}}>
                    
                    <Clock city={(misc.length > 0) && misc[0].city} dayImage={(misc.length > 0) && misc[0].day_image} nightImage={(misc.length > 0) && misc[0].night_image} textColor={(themes.length > 0) && themes[0].color} />

                    <div className="ads-box">
                      <img className="ads" src={adImageUrl} alt="ads" />
                    </div>

                    <div className="promotion-box">
                      <img className="promotion" src={promotionImageUrl} alt="promotion" />
                    </div>

                    
                    <div className="button-section">
                      <button className="btn-venue" style={{backgroundColor: (themes.length > 0) && themes[0].browse_venue_ribbon_color, color: (themes.length > 0) && themes[0].browse_venue_text_color}} onClick={toggleDrawer}>
                        Browse Venue
                      </button>
                    </div>
                  </div>
                }
                
              </div>
            </div>
          ) : (
            <div className="floor-container">
            
              <div style={{position: 'relative'}}>

                <div className="row">
                  <img style={{width: '100%', height: '5vh', objectFit: 'cover'}} src={bannerImageUrl} alt="banner" />
                </div>
      
                <Slider
                  slideIndex={slideIndex}
                  buttonWidthPercent={buttonWidthPercent}
                  slides={levels}
                  activeButton={activeButton}
                  handleButtonClick={handleSliderButtonClick}
                />
      
                <div className="map">
      
                  <div className="row g-0">
      
                    <div className={`${!mapVisible ? "col-12" : "col-6"}`}>
                      <MyMapCheckTwo mainMapContainerRef={mainMapContainerRef} type={orientation} bgColor={(themes.length > 0) && themes[0].map_container_color} />
                    </div>
                    <div className="col-6">
                      {mapVisible && (
                        <MyMapCheck secondMapContainerRef={secondMapContainerRef} type={orientation} bgColor={(themes.length > 0) && themes[0].map_container_color} />
                      )}
                    </div>
                  </div>
      
                </div>
                
                <OptionsContainer baseFloor={kiosksData.levelid} poi={poi} handleClick={handleClick} handleClearPoi={handleClearPoi} handleResetButtonClick={handleResetButtonClick} buttonColor={(themes.length > 0) && themes[0].button_color} bgColor={(themes.length > 0) && themes[0].options_ribbon_color} />

              </div>
    
              <div style={{height: '100%'}}>

                <div className="container d-flex flex-row">

                    <button
                      className="dropdown-toggle btn btn-select"
                      onClick={handleDropdownToggle}
                      ref={categoryButtonRef}
                    >
                      Categories
                    </button>

                    <MapSearchBar
                      handleSearch={handleSearch}
                      handleSuggestionClick={handleSuggestionClick}
                      suggestions={suggestions}
                      searchTerm={searchTerm}
                    />

                </div>

                <div className="container d-flex" ref={categoryDropdownRef}>
                  {categoryDropdown && (
                    <MapCategory
                      categories={categories}
                      handleCategorySelection={handleCategorySelection}
                      selectedCategory={selectedCategory}
                    />
                  )}
                </div>

                <div className="container d-flex" ref={categoryDropdownRef}>
                  {categoryDropdown && (
                    <MapCategory
                      categories={categories}
                      handleCategorySelection={handleCategorySelection}
                      selectedCategory={selectedCategory}
                    />
                  )}
                </div>

                <FilteredStores filteredCompanies={filteredCompanies} navigateToDashboard={navigateToDashboard} />
                  
              </div>
            </div>
          )}
      </>
  );

}