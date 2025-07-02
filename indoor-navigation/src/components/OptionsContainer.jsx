import Poi from "./Poi";
import './CSS/OptionsContainer.css'

import { Link } from "react-router-dom";

import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faHouse, faRotate} from "@fortawesome/free-solid-svg-icons";

export default function OptionsContainer({baseFloor, poi, handleClick, handleClearPoi, handleResetButtonClick, buttonColor, bgColor}) {

    return (
        <div className="options-container" style={{backgroundColor: bgColor}}>
            
            <div className="misc-container">
                <Link to={"/"} className="round-link" style={{backgroundColor: buttonColor}}>
                    <FontAwesomeIcon icon={faHouse} size="2xl" style={{ color: "white" }} />
                </Link>

                <Link to={`/floor/v/${baseFloor}`} className="round-link" onClick={handleResetButtonClick} style={{backgroundColor: buttonColor}}>
                    <FontAwesomeIcon icon={faRotate} size="2xl" style={{ color: "white" }} />
                </Link>
                
            </div>
            
            <Poi
                poi={poi}
                handleClick={handleClick}
                handleClearPoi={handleClearPoi}
                buttonColor={buttonColor}
            />

        </div>
    );
};