import { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";

import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faHouse, faMap} from "@fortawesome/free-solid-svg-icons";

import {
  getCategory,
  getCompanies,
  getVideos,
  initializeDatabaseIfNeeded,
  getColors,
  getKiosk
} from "../db/convertSQlite.js";

import "./CSS/index.css";
import "./CSS/companies.css";

export default function Index() {

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

  const Kiosks = localStorage.getItem("selectedKiosk");

  const [kiosksData, setkiosksData] = useState([]);

  const [themes, setThemes] = useState([]);
  const [categories, setCategories] = useState([]);
  const [companies, setCompanies] = useState([]);
  const [videos, setVideos] = useState([]);

  const [isLoading, setIsLoading] = useState(false);

  const [searchTerm, setSearchTerm] = useState("");
  const [history, setHistory] = useState([]);

  const [suggestions, setSuggestions] = useState([]);
  const navigate = useNavigate();

  useEffect(() => {
    const fetchData = async () => {
      
      await initializeDatabaseIfNeeded();
      
      setIsLoading(true);
      
      try {
        
        const fetchedCategories = await getCategory();
        const fetchedCompanies = await getCompanies();
        const fetchedVideos = await getVideos();
        const fetchedColors = await getColors();
        const fetchedKiosks = await getKiosk(Kiosks);

        setCategories(fetchedCategories);
        setCompanies(fetchedCompanies);
        setVideos(fetchedVideos);
        setThemes(fetchedColors);        
        setkiosksData(fetchedKiosks);

      }
      catch (error) {
        console.error("Error fetching data:", error);
      }
      finally {
        setIsLoading(false);
      }
    };

    fetchData();
  }, []);

  

  const getVideoSource = () => {
    const laravelURL = import.meta.env.VITE_LARAVEL_URL;
    
    if (videos.length > 0) {      
      for (const video of videos) {
        if((video.video_type === 'landscape') && orientation.startsWith('landscape'))
        {
          return `${laravelURL}/api/videos/${video.path.split('/').pop()}`;
        }
        else if((video.video_type === 'portrait') && orientation.startsWith('portrait'))
        {
          return `${laravelURL}/api/videos/${video.path.split('/').pop()}`;
        }
      }
    }

    return "/assets/screensaver.mp4";
  };

  const getVideoType = () => {
    if (videos.length > 0) {      
      for (const video of videos) {
        if((video.video_type === 'landscape') && orientation.startsWith('landscape'))
        {
          return `video/${video.type}`;
        }
        else if((video.video_type === 'portrait') && orientation.startsWith('portrait'))
        {
          return `video/${video.type}`;
        }
      }
    }
    else {
      return "video/mp4";
    }
  }
  

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

  const handleSuggestionClick = (suggestion) => {

    let existingData = null;
    try {
      existingData = JSON.parse(localStorage.getItem("user_search_data"));
      console.log(existingData);
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
      kiosk_id: kiosksData[0].id,
      store_id: suggestion.id,
      timestamp: formattedTimestamp
    };

    console.log(kiosksData);
  
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

  const navigateToDashboard = (storeName, levelid) => {
    navigate(`/navigation/${storeName}/${levelid}`);
  };

  const videoContainerStyle = {
    position: "fixed",
    top: 0,
    left: 0,
    width: "100vw",
    height: "100vh",
    zIndex: -1, // Video is behind the content
    objectFit: "cover",
    overflow: "hidden",
  };
  
  const overlayStyle = {
    position: "relative", // Ensures that search and category sections are above the video
    zIndex: 1,
    overflow: 'hidden'
  };
  
  const footerStyle = {
    position: "absolute",
    bottom: 0,
    width: "100%",
    display: "flex",
    justifyContent: "center",
    padding: "10px",
  };

  if (isLoading) {
    return(
      <h1>Loading...</h1>
    )
  }
  
  return (
    <div>
      {/* Video as Background */}
      <video key={getVideoSource()} style={videoContainerStyle} autoPlay loop muted>
        <source src={getVideoSource()} type={getVideoType()} />
        Your browser does not support the video tag.
      </video>
  
      {/* Content Overlay */}
      <div style={overlayStyle}>
        {/* Search Bar */}
        <div className="container py-2">
          <center>
            <div className="search-wrapper">
              <input
                type="text"
                placeholder="Search Navigation..."
                className="form-control me-2"
                style={{ borderRadius: 20 }}
                autoComplete="off"
                onChange={handleSearch}
                value={searchTerm}
              />
              {suggestions.length > 0 && (
                <ul className="suggestions-dropdown">
                  {suggestions.map((company) => (
                    <li
                      key={company.id}
                      className="suggestion-item"
                      onClick={() => handleSuggestionClick(company)}
                    >
                      {company.storename + " : " + company.category}
                    </li>
                  ))}
                </ul>
              )}
            </div>
          </center>
        </div>
  
      {/* Categories */}
      <div style={{ backgroundColor: 'rgba(0, 0, 0, 0.5)' }}>
        <div className="container-fluid justify-content-center">
          <ul className="category-list"> {/* Flex alignment */}
            {categories.map((cat, index) => (
              <li className="category-item" key={index}> {/* Each category item */}
                <Link to={`/category/${cat.category}`} className="nav-link text-white text-center">
                  <img
                    src={`/category/${cat.category.replace(/\s+/g, "-")}.png`}
                    alt={cat.category}
                    style={{ height: '60px' }}
                  />
                  <div>{cat.category}</div>
                </Link>
              </li>
            ))}
          </ul>
        </div>
      </div>

      </div>
        {/* Footer Section */}
        <div className="bottom-container" style={footerStyle}>
          <Link to={"/"} className="round-link" style={{backgroundColor : (themes.length > 0) && themes[0].button_color}}>
            <FontAwesomeIcon icon={faHouse} size="2xl" style={{ color: "white" }} />
          </Link>
  
          {(kiosksData.length > 0) && 
            <Link to={`/floor/v/${kiosksData[0].levelid}`} className="round-link" style={{backgroundColor : (themes.length > 0) && themes[0].button_color}}>
              <FontAwesomeIcon icon={faMap} size="2xl" style={{ color: "white" }} />
            </Link>
          }
        </div>
    </div>
  );
  
};