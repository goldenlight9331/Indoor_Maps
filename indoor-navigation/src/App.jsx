import { useEffect, useState } from "react";
import { Routes, Route, Navigate } from "react-router-dom";
import { useSelector } from "react-redux";
import Index from "./components/Index";
import Floor from "./components/floor.jsx";
import Mall from "./components/mall.jsx";
import More from "./components/More.jsx";
import Navigation from "./components/Navigation.jsx";
import Stores from "./components/Stores.jsx";
import Login from "./auth/Login.jsx";
import Logout from "./auth/Logout.jsx";
import BackArrow from "./components/img/back-arrow-bordered.svg";
import Register from "./auth/Register.jsx";
import Dashboard from "./components/Dashboard.jsx";
import MyMap from "./components/FloorPlanMap.jsx";
import Category from "./components/Category.jsx";
import FloorWrapper from "./components/FloorWrapper.jsx";
import * as L from "leaflet";

export default function App() {
  const { token } = useSelector((state) => state.auth);
  const laravelURL = import.meta.env.VITE_LARAVEL_URL;
  const [rotateReady, setRotateReady] = useState(false);
  const loadRotatePlugin = () => {
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = '/libs/leaflet-rotate-src.js';
      script.onload = resolve;
      script.onerror = reject;
      document.body.appendChild(script);
    });
  };

  useEffect(() => {
    setTimeout(function(){
      loadRotatePlugin().then(() => {
        setRotateReady(true);  
      });
    },5000);
    
    
  }, []);
  const handleSendLogs = () => {

    const logData = JSON.parse(localStorage.getItem('user_search_data'));

    console.log(logData);

    if (logData) {

      const payload = {
        suggestions: logData.suggestions,
      };

      fetch(`${laravelURL}/api/store-logs`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload)
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log('Logs sent successfully:', data);

              // Clear the relevant fields from localStorage after successful submission            
              localStorage.removeItem('user_search_data');
          } else {
              console.log('Error:', data.error || 'Unknown error occurred');
          }
      })
      .catch(error => {
          console.error('Error sending logs:', error);
      });
    }
    else {
      console.log('No log data or kiosk information found in localStorage');
    }
  };
  
  

  useEffect(() => {

    handleSendLogs();

    let intervalTime = 1800000;

    const fetchData = async () => {
      try {

        fetch(`${laravelURL}/api/log-interval`, {
          method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
              
              intervalTime = data.log_interval * 1000;

            }
            else {
              console.log('Error:', data.error || 'Unknown error occurred');
            }

            console.log(intervalTime);

            const interval = setInterval(() => {
              handleSendLogs();
            }, intervalTime);
        
            // Cleanup interval on unmount
            return () => clearInterval(interval);
        })
      }
      catch (error) {
        console.error("Error fetching data:", error);
      }
    };

    fetchData();

  }, []);


  return (
    <>
      <div className="">
        <Routes>
          <Route
            path="/"
            element={token ? <Index BackArrow={BackArrow} /> : <Login />}
          />
          <Route
            path="/mall"
            element={token ? <Mall BackArrow={BackArrow} /> : <Login />}
          />
          <Route
            path="/Show-map"
            element={token ? <MyMap BackArrow={BackArrow} /> : <Login />}
          />
          {/* <Route
            path="/floor/:STORENAME/:LEVELID"
            element={token ? <Floor BackArrow={BackArrow} /> : <Login />}
          /> */}

          <Route
              path="/floor/:STORENAME/:LEVELID"
              element={
                token ? (
                  rotateReady ? (
                    <Floor BackArrow={BackArrow} />
                  ) : (
                    <div>Loading map rotation plugin...</div>
                  )
                ) : (
                  <Login />
                )
              }
            />
          {/* <Route
            path="/floor/:STORENAME/:LEVELID"
            element={token ? <Floor BackArrow={BackArrow} /> : <Login />}
          /> */}
          <Route
            path="/floor"
            element={token ? <Floor BackArrow={BackArrow} /> : <Login />}
          />
          <Route
            path="/category/:CategoryName"
            element={token ? <Category BackArrow={BackArrow} /> : <Login />}
          />
          <Route
            path="/more"
            element={token ? <More BackArrow={BackArrow} /> : <Login />}
          />
          <Route
            path="/store"
            element={token ? <Stores BackArrow={BackArrow} /> : <Login />}
          />
          <Route
            path="/login"
            element={!token ? <Login /> : <Navigate to="/dashboard" />}
          />
          <Route
            path="/register"
            element={<Register BackArrow={BackArrow} />}
          />
          <Route
            path="/logout"
            element={!token ? <Logout /> : <Navigate to="/dashboard" />}
          />

          
          {/*<Route path="/dashboard" element={token ? <Dashboard /> : <Navigate to="/login" />}/>*/}
          
          <Route
            path="/navigation/:STORENAME/:LEVELID"
            element={<Navigation BackArrow={BackArrow} />}
          />
          <Route
            path="/navigation/:STORENAME/:LEVELID"
            element={
              !token ? <Login /> : <Navigate to="/navigation/:STORENAME" />
            }
          />
          <Route path="*" element={<h1>Error 404 Page not found !!</h1>} />
          {/* <Route path="/floorcheck/:STORENAME" element={<FloorCheck />} /> */}
        </Routes>
      </div>
    </>
  );
}
