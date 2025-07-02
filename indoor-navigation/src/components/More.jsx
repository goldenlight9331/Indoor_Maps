import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import PropTypes from "prop-types";

import { getCategory, forntEnd } from "../db/convertSQlite";

import styles from "./CSS/Category.module.css";
import back from "./category/back button.png";

export default function More({ BackArrow }) {
  const [bgImage, UpdateBgImage] = useState([]);
  const [category, setCategory] = useState([]);
  const fetchCatgory = async () => {
    try {
      const fetchedImages = await forntEnd();
      console.log("Image:", fetchedImages);
      UpdateBgImage(fetchedImages);
      const fetchedCategories = await getCategory();
      console.log("fetched Categories", fetchedCategories);
      setCategory(fetchedCategories);
    } catch (error) {
      console.error("Error fetching category:", error);
    }
  };
  useEffect(() => {
    fetchCatgory();
  }, []);
  let imageUrl = "";
  if (bgImage.length > 0) {
    //   const buffer = new Uint8Array(bgImage[0].home);
    //   let binaryString = "";
    //   for (let i = 0; i < buffer.length; i++) {
    //     binaryString += String.fromCharCode(buffer[i]);
    //   }
    imageUrl = `data:image/png;base64,${bgImage[0].more}`;
    // imageUrl = `data:image/jpeg;base64,${btoa(binaryString)}`;
  } else {
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
    <>
      <div style={divStyle}></div>
      <div className={`container-fluid ${styles["veues"]}`}>
        <div className="row">
          <div className="text-center">
            <h1 style={{ color: "white", fontSize: "30px" }}>
              EXPLORE THE VENUES
            </h1>
          </div>
        </div>
      </div>
      <div className="container-fluid mt-5">
        <div className="row">
          <div className="col-md-6 mx-auto">
            <div className={styles["category-box"]}>
              <div className="container mt-5">
                <div className="row">
                  {category.map((cat, index) => (
                    <div className="col-md-4 mt-1" key={index}>
                      <img
                        src={`/category/${cat.category.replace(
                          /\s+/g,
                          "-"
                        )}.png`}
                        alt="Spas"
                        style={{ marginLeft: "30px", marginBottom: "12px" }}
                      />
                      <button
                        className={`btn btn-block ${styles["custom-button"]}`}
                      >
                        <div style={{ textAlign: "center" }}>
                          {" "}
                          <Link
                            to={`/category/${cat.category}`}
                            key={index}
                          >{`   ${cat.category}`}</Link>
                        </div>
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div>
        <button className={styles["btn"]}>
          <Link to={"/"}>
            <img src={back} alt="Button Image" style={{ marginTop: "-20px" }} />
          </Link>
        </button>
      </div>
    </>
  );
}
More.propTypes = {
  BackArrow: PropTypes.string.isRequired,
};
