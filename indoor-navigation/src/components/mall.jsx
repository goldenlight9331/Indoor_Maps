import User from "./img/user.svg";
import Location from "./img/location.svg";
import Search from "./img/search.svg";
import Scheme from "./img/scheme2.svg";
export default function mall({ BackArrow }) {
  return (
    <>
      <main>
        <section className="section__mall">
          <div className="section__mall-head">
            <div className="back-bordered">
              <a href="./index.html">
                <img className="back-svg" src={BackArrow} alt="/" />
              </a>
            </div>
            <div className="head-title">
              <h3>Demo Mall Block G</h3>
            </div>
          </div>
          <div className="action-section">
            <div className="action-wrapper">
              <div className="action-btns">
                <button className="action-btn">
                  <span className="icon">
                    <img src={User} alt="/" />
                  </span>
                  <span className="btn-text">My location</span>
                </button>
                <button className="action-btn">
                  <span className="icon">
                    <img src={Location} alt="/" />
                  </span>
                  <span className="btn-text">Destination</span>
                </button>
                <button className="search-btn">
                  <span className="icon">
                    <img src={Search} alt="/" />
                  </span>
                  Search
                </button>
              </div>
            </div>
            <div className="map-section">
              <img src={Scheme} alt="/" />
            </div>
          </div>
        </section>
      </main>
    </>
  );
}
