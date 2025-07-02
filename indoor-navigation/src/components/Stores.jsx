import { useState, useEffect } from "react";
import { getCompanies } from "../db/convertSQlite";
// import loadDatabase from '../db/convertSQlite';
// import Item from "./img/shop-item1.png";
// import Item2 from "./WF_Level_0/Images/3.webp";
import Visit from "./img/visit.svg";
import Mapp from "./img/map.svg";
import { Link } from "react-router-dom";
import PropTypes from "prop-types";

export default function Stores({ BackArrow }) {
  const [companies, setCompanies] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const companiesPerPage = 8; // Change this value as needed
  const totalPageCount = Math.ceil(companies.length / companiesPerPage);

  const fetchCompanies = async () => {
    try {
      const fetchedCompanies = await getCompanies();
      console.log("Fetched companies:", fetchedCompanies); // Log fetched data
      setCompanies(fetchedCompanies);
    } catch (error) {
      console.error("Error loading database:", error);
    }
  };

  useEffect(() => {
    fetchCompanies();
  }, []);

  const nextPage = () => {
    if (currentPage < totalPageCount) {
      setCurrentPage((prevPage) => prevPage + 1);
    }
  };

  const previousPage = () => {
    if (currentPage > 1) {
      setCurrentPage((prevPage) => prevPage - 1);
    }
  };

  const startIndex = (currentPage - 1) * companiesPerPage;
  const endIndex = startIndex + companiesPerPage;
  const visibleCompanies = companies.slice(startIndex, endIndex);

  return (
    <>
      <main>
        <section className="section__stores">
          <div className="section__stores-head">
            <div className="back">
              <Link to={"/"}>
                <i className="fa-solid fa-arrow-left"></i>
                <img className="back-svg" src={BackArrow} alt="Back" />
              </Link>
            </div>
            <div className="head-name">
              <p>stores</p>
            </div>
          </div>
          <div className="swiper-container">
            <div className="swiper-wrapper">
              <div className="swiper-slide">
                <div className="items">
                  {visibleCompanies.map((company, index) => {
                    const byteArray = new Uint8Array(
                      companies[index].image_data
                    );
                    const base64String = btoa(
                      String.fromCharCode.apply(null, byteArray)
                    );
                    const dataURL = `data:image/png;base64,${base64String}`;
                    return (
                      <div className="item" key={index}>
                        <div className="image-container">
                          <img src={dataURL} alt={company.images} />
                        </div>
                        <div className="text-container">
                          <h2>{company.storename}</h2>
                        </div>
                        <div className="links">
                          <a href={company.visitLink} className="link-visit">
                            <img src={Visit} alt="Visit" />
                            Visit
                          </a>
                          <a href={company.mapLink} className="link-map">
                            <img src={Mapp} alt="Map" />
                            Map
                          </a>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>
            </div>
            <div className="pagi">
              <div className="page-button-prev" onClick={previousPage}>
                Previous Page
              </div>
              <div className="pagination-container">
                {[...Array(totalPageCount)].map((_, index) => (
                  <div
                    key={index}
                    className={`pagination-dot ${
                      index + 1 === currentPage ? "active" : ""
                    }`}
                    onClick={() => setCurrentPage(index + 1)}
                  />
                ))}
              </div>
              <div className="page-button-next" onClick={nextPage}>
                Next Page
              </div>
            </div>
          </div>
        </section>
      </main>
    </>
  );
}
Stores.propTypes = {
  BackArrow: PropTypes.string.isRequired,
};
