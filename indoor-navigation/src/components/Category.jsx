import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";

import { getCompanies, forntEnd } from "../db/convertSQlite";

import styles from "./CSS/Store.module.css";

import { faArrowLeft } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

const Category = () => {
  const { CategoryName } = useParams();
  const [filteredCompanies, setFilteredCompanies] = useState([]);
  const [bgImage, updateBgImage] = useState([]);

  useEffect(() => {
    const fetchCompaniesByCategory = async () => {
      try {

        const fetchedImages = await forntEnd();
        updateBgImage(fetchedImages);

        const allCompanies = await getCompanies();

        if (CategoryName) {
          const filtered = allCompanies.filter((company) => company.category === CategoryName);

          setFilteredCompanies(filtered);
        }
        else {
          setFilteredCompanies(allCompanies);
        }
      }
      catch (error) {
        console.error("Error fetching companies:", error);
      }
    };

    fetchCompaniesByCategory();
  }, [CategoryName]);

  let imageUrl = "";
  if (bgImage.length > 0) {
    imageUrl = `data:image/png;base64,${bgImage[0].home}`;
  }
  else {
    console.log("bgImage is empty");
  }

  const divStyle = {
    backgroundImage: `url(${imageUrl})`,
    backgroundSize: "cover",
    backgroundRepeat: "no-repeat",
    height: "100vh",
    position: "fixed",
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    zIndex: -1,
  };


  return (
    <div>
      <div style={divStyle}></div>

      <div className="container">
        <div className="row align-items-center"> {/* Flexbox row for alignment */}
          <div className="col-auto">
            <Link to={"/"} style={{ textDecoration: "none" }}>
              <div
                style={{
                  display: "inline-flex",
                  alignItems: "center",
                  padding: "5px 10px",
                  backgroundColor: "#B02F4F", // Background color for emphasis
                  borderRadius: "10px",
                  transition: "transform 0.3s ease, background-color 0.3s ease"
                }}
                className="hover-emphasis"
              >
                <FontAwesomeIcon
                  icon={faArrowLeft}
                  size="3x"
                  style={{ color: "white" }}
                />
              </div>
            </Link>
          </div>

          <div className="col text-center"> {/* Center the H1 */}
            <h1 style={{ color: "white", fontSize: '50px', whiteSpace: "nowrap" }}>
              {CategoryName || "All Categories"}
            </h1>
          </div>

          <div className="col-auto"> {/* Empty col to balance layout */}
            {/* Optional content here if needed */}
          </div>
        </div>
      </div>


      <div className="container mt-5">
        <div className="row row-cols-1 row-cols-md-5 g-4">
          {filteredCompanies.map((company, index) => (
            <div className="col" key={index}>
              <Link
                to={`/navigation/${company.storename}/${company.levelid}`}
              >
                <div
                  className={`card h-100 ${styles["animated-card"]}`}
                  style={{ borderRadius: 20 }}
                >
                  <img
                    src={`data:image/png;base64,${company.image_data}`}
                    style={{ borderRadius: 20 }}
                    height={200}
                    className="card-img-top"
                    alt="Company"
                  />
                  <div
                    className="text-center text-dark"
                    style={{ borderRadius: 20, padding: 5 }}
                  >
                    <h5 className="card-title">{company.storename}</h5>
                  </div>                  
                </div>
              </Link>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default Category;
