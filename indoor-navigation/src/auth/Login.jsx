import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom"; // For navigation
import { useLoginUserMutation } from "../services/userAuthApi";
import { getToken, storeToken } from "../services/LocalStorageService"; // Token management
import Modal from "react-bootstrap/Modal";
import { setUserToken } from "../features/authSlice";
import { useDispatch } from "react-redux";
import LoginCss from "./register.module.css";
import {
  getAllKiosk,
  initializeDatabaseIfNeeded,
} from "../db/convertSQlite.js";

export default function Login() {
  const [error, setError] = useState({
    status: false,
    msg: "",
  });
  const [show, setShow] = useState(false);
  const [loading, setLoading] = useState(true); // Loading state
  const [loginUser] = useLoginUserMutation();
  const [kiosks, setKiosks] = useState([]);
  const [selectedValue, setSelectedValue] = useState("");
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const handleClose = () => setShow(false);
  const handleShow = () => setShow(true);

  // Handle kiosk selection
  const handleSelectChange = (event) => {
    setSelectedValue(event.target.value);
  };

  const handleSubmitKiosks = (event) => {
    event.preventDefault();
    localStorage.setItem("selectedKiosk", selectedValue);
  };

  // Fetch kiosks and initialize the database
  useEffect(() => {
    const fetchData = async () => {
      try {
        await initializeDatabaseIfNeeded();
        const fetchedKiosks = await getAllKiosk();
        setKiosks(fetchedKiosks);
      } catch (error) {
        console.error("Error fetching kiosks:", error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  // Handle login form submission
  const handleSubmit = async (e) => {
    e.preventDefault();
    const data = new FormData(e.currentTarget);
    const actualData = {
      email: data.get("email"),
      password: data.get("password"),
    };

    if (!selectedValue || selectedValue === "Select Kiosks") {
      setError({ status: true, msg: "Please select a kiosk before logging in." });
      return;
    }

    if (actualData.email && actualData.password) {
      try {
        const res = await loginUser(actualData);

        if (res.data && res.data.status === "success") {
          // Store token in localStorage and Redux
          storeToken(res.data.token);
          dispatch(setUserToken({ token: res.data.token }));

          // Clear error message and redirect to home page
          setError({ status: false, msg: "" });
          navigate("/"); // Redirect to home
        } else if (res.error && res.error.data.status === "failed") {
          setError({ status: true, msg: res.error.data.message });
        }
      } catch (err) {
        console.error("Login error:", err);
        setError({ status: true, msg: "An unexpected error occurred." });
      }
    } else {
      setError({ status: true, msg: "All Fields are Required" });
    }
  };

  // Ensure token is updated in Redux when component loads
  useEffect(() => {
    const sessionToken = getToken(); // Retrieve token from localStorage
    if (sessionToken) {
      dispatch(setUserToken({ token: sessionToken }));
    }
  }, [dispatch]);

  return (
    <>
      <Modal show={show} onHide={handleClose}>
        <Modal.Body>
          <center>
            <h4>Select Kiosk</h4>
          </center>
          <center>
            <form onSubmit={handleSubmitKiosks}>
              <select
                className="custom-select"
                value={selectedValue}
                onChange={handleSelectChange}
              >
                <option>Select Kiosks</option>
                {kiosks.map((kiosk, index) => (
                  <option key={index} value={kiosk.name}>
                    {kiosk.name}
                  </option>
                ))}
              </select>
              <br />
              <button
                className="btn btn-sm btn-primary"
                onClick={handleClose}
                type="submit"
              >
                Select
              </button>
            </form>
          </center>
        </Modal.Body>
      </Modal>
      <div className={LoginCss["module-class-body"]}>
        <div className={LoginCss["module-login-container"]}>
          <h2 className={LoginCss["module-heading"]}>Indoor Navigation</h2>
          <form id="login-form" onSubmit={handleSubmit}>
            <input
              type="text"
              placeholder="UserName"
              name="email"
              className={LoginCss["module-form-control"]}
            />
            <input
              type="password"
              name="password"
              placeholder="Password"
              className={LoginCss["module-form-control"]}
            />

            <a
              onClick={handleShow}
              className={`mb-2 btn btn-danger ${LoginCss["module-btn-login"]}`}
            >
              Select Kiosks
            </a>
            <button
              type="submit"
              className={LoginCss["module-btn-login"]}
              disabled={loading}
            >
              {loading ? "Loading..." : "Login"}
            </button>
          </form>
          {error.status && (
            <div
              id="error-message"
              className={LoginCss["module-error-message"]}
            >
              {error.msg}
            </div>
          )}
        </div>
      </div>
    </>
  );
}
