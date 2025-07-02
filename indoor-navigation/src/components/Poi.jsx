import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCircle, faSquareParking, faTaxi, faUserTie, faElevator, faChildReaching, faStairs, faSignInAlt, faMoneyBillAlt, faToilet, faRestroom, faSignOutAlt, faStar, faInfoCircle, faBatteryFull, faPoundSign } from "@fortawesome/free-solid-svg-icons";
import "./CSS/poi.css";

export default function Poi({ poi, handleClick, handleClearPoi, buttonColor }) {
  const uniquePoiNames = [...new Set(poi.map((poiItem) => poiItem.POINAME))];

  const iconMap = {
    Escalator: faStairs,
    Elevator: faElevator,
    Stairs: faStairs,
    Enter: faSignInAlt,
    ATM: faMoneyBillAlt,
    Toilet: faToilet,
    WC: faToilet,
    Restroom: faRestroom,
    Exit: faSignOutAlt,
    Favourite: faStar,
    Info: faInfoCircle,
    "Mobile Charger": faBatteryFull,
    "Kid Slide": faChildReaching,
    "Pound Sterling": faPoundSign,
    "Car Parking": faSquareParking,
    "Car Park": faSquareParking,
    "Cab Service": faTaxi,
    "Board Person": faUserTie,
  };

  return (
    <div className="poi-container">
        <style>
        {`
          .poi-container::-webkit-scrollbar-thumb {
            background-color: ${buttonColor};
          }
        `}
        </style>
      <ul className="poi-list">
        <div
          className="poi-item"
          onClick={() => {
            handleClearPoi();
          }}
        >
          <div style={{ backgroundColor: buttonColor }} className="inner-border">
            <FontAwesomeIcon icon={faCircle} size="2xl" style={{ color: "white" }} />
          </div>
        </div>

        {uniquePoiNames.map((poiName, index) => (
          <div
            className="poi-item"
            key={index}
            onClick={() => handleClick(poiName)}
          >
            <div className="poi-icon">
              {iconMap[poiName] ? (
                <div style={{ backgroundColor: buttonColor }} className="inner-border">
                  <FontAwesomeIcon icon={iconMap[poiName]} size="2xl" style={{ color: "white" }}/>
                </div>
              ) : (
                <span>{poiName}</span> // Fallback if no icon is mapped
              )}
            </div>
          </div>
        ))}
      </ul>
    </div>
  );
}