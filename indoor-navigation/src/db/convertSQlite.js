import SPL from "./spl-web.js";

let spatialiteDBInstance = null;
let isDatabaseLoaded = false;
let initializationPromise = null;

const laravelURL = import.meta.env.VITE_LARAVEL_URL;

const initializeSpatialiteDB = async () => {
  const response = await fetch(`${laravelURL}/api/company`);


  let dbArrayBuffer = null;
  const clonedResponse = response.clone(); // Clone the response to read the body again
  const responseText = await response.text(); 

  try {
    const responseData = JSON.parse(responseText); // Attempt to parse as JSON

    // Check if the response contains a message key
    if (responseData.message && responseData.message === "License Expired") {
      alert("License Expired");
      return; // Stop further execution
    }
  } catch (e) {
    // Handle the case where the response is not JSON and is likely an ArrayBuffer
    dbArrayBuffer = await clonedResponse.arrayBuffer();
  }


  const spl = await SPL();
  // const dbArrayBuffer = await fetch(`${laravelURL}/api/company`).then(
  //   (response) => response.arrayBuffer()
  // );
  spatialiteDBInstance = await spl.db(dbArrayBuffer);

  await spatialiteDBInstance.exec("SELECT InitSpatialMetaDataFull()");
  const getLevels = async () => {
    let selectResult = await spatialiteDBInstance.exec(
      "SELECT DISTINCT levelid FROM lots ORDER BY levelid"
    );
    return selectResult.get.objs;
  };
  const processFloors = async () => {
    let floors = await getLevels();
    for (let index = 0; index < floors.length; index++) {
      let floorNumber = floors[index].levelid.toString().replace("-", "_");
      let routingNodesQuery = `SELECT CreateRoutingNodes(NULL, 'walkways_${floorNumber}', 'geom','node_from', 'node_to');`;
      await spatialiteDBInstance.exec(routingNodesQuery);
      await spatialiteDBInstance.exec(
        `SELECT CreateRouting('routes_data_${floorNumber}', 'routes_${floorNumber}', 'walkways_${floorNumber}', 'node_from', 'node_to', 'geom', NULL, NULL, 1, 1);`
      );
      // console.log(`Processing floor ${floorNumber}`);
    }
  };
  await processFloors();
  
  isDatabaseLoaded = true;
  console.log("DATABASE LOADED");
  return spatialiteDBInstance;
};

const initializeDatabaseIfNeeded = async () => {
  if (!isDatabaseLoaded && !initializationPromise) {
    initializationPromise = initializeSpatialiteDB();
  }
  await initializationPromise;
};

const getCompanies = async () => {
  let selectResult = await spatialiteDBInstance.exec("SELECT * FROM companies");
  return selectResult.get.objs;
};

const getLevels = async () => {
  let selectResult = await spatialiteDBInstance.exec(
    "SELECT distinct levelid FROM lots order by levelid"
  );
  return selectResult.get.objs;
};

const getFloorPlan = async (LEVELNAME) => {
  let selectResult = await spatialiteDBInstance.exec(
    `select AsGeoJson(lots.geom) as geojson,levelcode,category,STORENAME as storename,color from lots LEFT JOIN (select STORENAME , geom from companies WHERE levelid = '${LEVELNAME}') com on ST_Within(com.geom , lots.geom) WHERE levelid = '${LEVELNAME}'`
  );
  return selectResult.get.objs;
};
const getFloorPlanSecond = async (LEVELID) => {
  let selectResult = await spatialiteDBInstance.exec(
    `select AsGeoJson(lots.geom) as geojson,levelcode,category,STORENAME as storename from lots LEFT JOIN (select STORENAME , geom from companies WHERE levelid = '${LEVELID}') com on ST_Within(com.geom , lots.geom) WHERE levelid = '${LEVELID}'`
  );
  return selectResult.get.objs;
};

const getWalkways = async () => {
  let selectResult = await spatialiteDBInstance.exec(
    "select AsGeoJson(geom) as geojson from walkways_0"
  );
  return selectResult.get.objs;
};

const getRouteToAnotherFloor = async (
  baseFloorNumber,
  mediumToUse,
  targetFloorNumber,
  companyName,
  kioskPoint
) => {
  let targetFloor = targetFloorNumber.toString().replace("-", "_");
  let baseFloor = baseFloorNumber.toString().replace("-", "_");
  if (baseFloorNumber.toString() == targetFloorNumber.toString()) {
    let sqlToRun = `
            SELECT * FROM routes_${baseFloor} WHERE NodeFrom = (SELECT node_from FROM walkways_${baseFloor}
                ORDER BY Distance(geom, makepoint(${kioskPoint[1]}, ${kioskPoint[0]})) LIMIT 1) 
            AND NodeTo = (SELECT node_from FROM walkways_${baseFloor} 
                ORDER BY Distance(geom, 
                    (SELECT geom FROM companies WHERE "storename" = '${companyName}')) LIMIT 1)
        `;

    let selectResult = await spatialiteDBInstance.exec(sqlToRun).get.objs;

    let companyGeometrySql = `SELECT AsGeoJson(geom) as companygeojson FROM companies WHERE "storename" = '${companyName}' AND levelid = ${targetFloorNumber}`;
    let companyResults = await spatialiteDBInstance.exec(companyGeometrySql).get
      .objs;

    return {
      routing_type: "singlefloor",
      routing_data: [selectResult, companyResults],
    };
    return selectResult;
  } else {
    if (mediumToUse == null) {
      return null;
    }
    try {
      let sqlToRun = `
                SELECT * FROM routes_${baseFloor}
                WHERE NodeFrom = (SELECT node_from FROM walkways_${baseFloor}
                ORDER BY Distance(geom, makepoint(${kioskPoint[1]}, ${kioskPoint[0]})) LIMIT 1)
                AND NodeTo = (SELECT node_to FROM walkways_${baseFloor}
                ORDER BY Distance(geom, (SELECT geom FROM poi WHERE "poiname" = '${mediumToUse}' AND levelid = ${baseFloorNumber}
                ORDER BY Distance(geom, makepoint(${kioskPoint[1]}, ${kioskPoint[0]})) LIMIT 1)) LIMIT 1)
            `;

      let sqlToRunTwo = `
                SELECT * FROM routes_${targetFloor}
                WHERE NodeFrom = (SELECT node_from FROM walkways_${targetFloor}
                ORDER BY Distance(geom, (SELECT geom FROM poi WHERE "poiname" = '${mediumToUse}' AND levelid = ${baseFloorNumber}
                ORDER BY Distance(geom, makepoint(${kioskPoint[1]}, ${kioskPoint[0]})) LIMIT 1)) LIMIT 1)
                AND NodeTo = (SELECT node_to FROM walkways_${targetFloor}
                ORDER BY Distance(geom, (SELECT geom FROM companies WHERE "storename" = '${companyName}' AND levelid = ${targetFloorNumber})) LIMIT 1)
            `;

      let poiGeometrySql = `SELECT AsGeoJson(geom) as poigeojson FROM poi WHERE "poiname" = '${mediumToUse}' AND levelid = ${baseFloorNumber}
                ORDER BY Distance(geom, makepoint(${kioskPoint[1]}, ${kioskPoint[0]})) LIMIT 1`;

      let companyGeometrySql = `SELECT AsGeoJson(geom) as companygeojson FROM companies WHERE "storename" = '${companyName}' AND levelid = ${targetFloorNumber}`;

      let mainRouteResults = await spatialiteDBInstance.exec(sqlToRun).get.objs;
      let secondRouteResults = await spatialiteDBInstance.exec(sqlToRunTwo).get
        .objs;
      let nearestPoiResults = await spatialiteDBInstance.exec(poiGeometrySql)
        .get.objs;
      let companyResults = await spatialiteDBInstance.exec(companyGeometrySql)
        .get.objs;

      return {
        routing_type: "multifloor",
        routing_data: [
          mainRouteResults,
          secondRouteResults,
          nearestPoiResults,
          companyResults,
        ],
      };
    } catch (error) {
      console.error("Error executing query:", error);
      return null;
    }
  }
};

const getRoute = async (companyName) => {
  try {
    // let sqlToRun = `SELECT * FROM walkways
    //         ORDER BY Distance(geom,
    //             (SELECT geom FROM companies WHERE "STORENAME" = '${companyName}')) LIMIT 1`;

    let sqlToRun = `
            SELECT * FROM routes_0 WHERE NodeFrom = 1 
            AND NodeTo = (SELECT node_from FROM walkways_0 
                ORDER BY Distance(geom, 
                    (SELECT geom FROM companies WHERE "STORENAME" = '${companyName}')) LIMIT 1)
        `;

    // let sqlToRun = `SELECT * FROM byfoot WHERE PointFrom = ST_GeomFromText('POINT(-0.2183156 51.5055935)') and PointTo = ST_GeomFromText('POINT(-0.2218726 51.5086789)')`;
    // let sqlToRun = "SELECT makepoint(-0.21935326,51.50615005,4326) as pnt";
    let selectResult = await spatialiteDBInstance.exec(sqlToRun);
    return selectResult.get.objs;
  } catch (error) {
    console.error("Error executing query:", error);
    return null;
  }
};

const getData = async (storename) => {
  try {
    const selectResult = await spatialiteDBInstance.exec(
      "SELECT ST_AsText(geom) as geometry ,* FROM companies WHERE STORENAME = ?",
      [storename]
    );
    const companiesData = selectResult.get.objs || [];
    return companiesData;
  } catch (error) {
    console.error("Error fetching companies data:", error);
    throw new Error("Failed to fetch companies data");
  }
};
const getCompany = async (storename) => {
  try {
    const selectResult = await spatialiteDBInstance.exec(
      "SELECT ST_AsText(geom) as geometry ,* FROM companies WHERE STORENAME = ?",
      [storename]
    );
    const companiesData = (await selectResult.get.objs) || [];
    return companiesData;
  } catch (error) {
    console.error("Error fetching companies data:", error);
    throw new Error("Failed to fetch companies data");
  }
};
const getCategory = async () => {
  try {
    let selectResult = await spatialiteDBInstance.exec(
      "SELECT DISTINCT category FROM companies"
    );
    return selectResult.get.objs;
  } catch (error) {
    console.error("Error fetching categories:", error);
    return []; // Return an empty array or handle the error as needed
  }
};

const getPOI = async (levelid) => {
  try {
    let selectResult = await spatialiteDBInstance.exec(
      `SELECT ST_AsText(geom) as geometry, * FROM poi WHERE levelid = ?`,
      [levelid]
    );
    return selectResult.get.objs;
  } catch (error) {
    console.error("Error fetching POI:", error);
    return []; // Return an empty array or handle the error as needed
  }
};

const forntEnd = async () => {
  try {
    let selectResult = await spatialiteDBInstance.exec(
      `SELECT * FROM  fornt_ends`
    );
    return selectResult.get.objs;
  } catch (error) {
    console.error("Error fetchingfornt_ends:", error);
    return [];
  }
};

const getAds = async () => {
  try {
    let selectResult = await spatialiteDBInstance.exec(`SELECT * FROM  ads`);
    return selectResult.get.objs;
  } catch (error) {
    console.error("Error ads:", error);
    return [];
  }
};

const getPromotions = async () => {
  try {
    let selectResult = await spatialiteDBInstance.exec(`SELECT * FROM  promotions`);
    return selectResult.get.objs;
  }
  catch (error) {
    console.error("Error promotions:", error);
    return [];
  }
};

const getBanners = async () => {
  try {
    let selectResult = await spatialiteDBInstance.exec(`SELECT * FROM  banners`);
    return selectResult.get.objs;
  }
  catch (error) {
    console.error("Error banners:", error);
    return [];
  }
};

const getVideos = async () => {
  try {
    let selectResult = await spatialiteDBInstance.exec(`SELECT * FROM  videos`);
    return selectResult.get.objs;
  }
  catch (error) {
    console.error("Error videos:", error);
    return [];
  }
};

const getColors = async () => {
  try {
    let selectResult = await spatialiteDBInstance.exec(`SELECT * FROM  themes`);
    return selectResult.get.objs;
  }
  catch (error) {
    console.error("Error colors:", error);
    return [];
  }
};

const getSettings = async () => {
  try {
    let selectResult = await spatialiteDBInstance.exec(`SELECT * FROM  settings`);
    return selectResult.get.objs;
  }
  catch (error) {
    console.error("Error Settings:", error);
    return [];
  }
};

const getMisc = async () => {
  try {
    let selectResult = await spatialiteDBInstance.exec(`SELECT * FROM  misc_settings`);
    return selectResult.get.objs;
  }
  catch (error) {
    console.error("Error Misc Settings:", error);
    return [];
  }
};

const getKiosk = async (seletedKiosks) => {
  try {
    let selectResult = await spatialiteDBInstance.exec(
      `SELECT * FROM  kiosks WHERE "name" = '${seletedKiosks}'`
    );
    return selectResult.get.objs;
  } catch (error) {
    console.error("Error on getting particular Kiosk:", error);
    return [];
  }
};

const getAllKiosk = async () => {
  try {
    let selectResult = await spatialiteDBInstance.exec(`SELECT * FROM kiosks;`);
    return selectResult.get.objs;
  } catch (error) {
    console.error("Error on getting all Kiosks", error);
    return [];
  }
};

export {
  getCompanies,
  getFloorPlan,
  getWalkways,
  getRoute,
  getData,
  getCategory,
  getPOI,
  getRouteToAnotherFloor,
  getFloorPlanSecond,
  getLevels,
  forntEnd,
  initializeDatabaseIfNeeded,
  getAds,
  getPromotions,
  getBanners,
  getVideos,
  getColors,
  getMisc,
  getCompany,
  getKiosk,
  getAllKiosk,
  getSettings
};
