import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { getData, forntEnd } from "../db/convertSQlite";

import Style from "./CSS/navigation.module.css";
import { faArrowLeft, faPhone, faGlobe } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

const Navigation = () => {

  const { STORENAME, LEVELID } = useParams();
  const [companies, setCompanies] = useState([]);
  const [loading, setLoading] = useState(true);
  const [bgImage, UpdateBgImage] = useState([]);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchCompanies = async () => {
      try {
        const fetchedImages = await forntEnd();
        UpdateBgImage(fetchedImages);

        const fetchedCompanies = await getData(STORENAME);
        
        setCompanies(fetchedCompanies);
        
        setLoading(false);
      }
      catch (error) {
        console.error("Error fetching companies:", error);
      
        setError("Error fetching companies");
      
        setLoading(false);
      }
    };

    fetchCompanies();
  }, [STORENAME, LEVELID]);

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;


  let imageUrl = "";
  if (bgImage.length > 0) {
    imageUrl = `data:image/png;base64,${bgImage[0].navigation}`;
  }
  else {
    console.log("bgImage is empty");
  }
  
  const company = companies.length > 0 ? companies[0] : null;

  const handleRedirect = () => {
    window.open(company.website, '_blank');
  };


  console.log(company);
  
  const divStyle = {
    backgroundImage: `url(${imageUrl})`,
    backgroundSize: "100vw 100vh",
    
    display: "flex",
    height: "100vh",
    
    flexDirection: "column",
    backgroundRepeat: "no-repeat",
  };
  
  return (
    <div style={divStyle}>

      <div className={Style["container-navigation"]} style={{ marginTop: 200 }}>

        <div className="container">
          <Link to={"/"}>
            <FontAwesomeIcon
              icon={faArrowLeft}
              size="2xl"
              style={{ color: "black" }}
            />
          </Link>
        </div>

        <div className="row">
          <div className={Style["mid-img"]}>
            <center>
              {company && (
                <img
                  className={Style["shoes-img"]}
                  src={`data:image/png;base64,${company.image_data}`}
                  alt="Company Logo"
                />
              )}
            </center>
          </div>

          <div
            className={`card p-5 ${Style["card"]}`}
            style={{ borderRadius: 20 }}
          >
            <div className={Style["card-content"]}>
              {company && (
                <center>
                
                  <h1>{company.storename}</h1>
                
                  <h4>Floor: {company.levelid}</h4>
                
                  {company.phone && <h5> <FontAwesomeIcon icon={faPhone} style={{ color: "black" }}/> {company.phone} </h5>}

                  {company.website && <h5 onClick={handleRedirect}> <FontAwesomeIcon icon={faGlobe} style={{ color: "black" }}/> {company.website}</h5>}
                
                </center>
              )}
            </div>

            {company && (
              <center>
                <Link
                  to={`/floor/${STORENAME}/${LEVELID}`}
                  className={Style["start-navigation-btn"]}
                >
                  Start Navigation
                </Link>
              </center>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default Navigation;
