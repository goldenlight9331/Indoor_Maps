import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useRegisterUserMutation } from "../services/userAuthApi";
import { storeToken } from "../services/LocalStorageService";
import styles from "./register.module.css"; // Import your CSS module

export default function Register() {
  const [error, setError] = useState({
    status: false,
    msg: "",
  });
  const Navigate = useNavigate();
  const [registerUser] = useRegisterUserMutation();

  const handleSubmit = async (e) => {
    e.preventDefault();
    const data = new FormData(e.currentTarget);
    const actualData = {
      name: data.get("name"),
      email: data.get("email"),
      password: data.get("password"),
    };
    if (actualData.name && actualData.email && actualData.password) {
      const res = await registerUser(actualData);
      storeToken(res.data.token);
      Navigate("/");
    } else {
      setError({ status: true, msg: "All Fields are Required" });
    }
  };

  return (
    <div className={styles["module-class-body"]}>
      {" "}
      {/* Use modular class names */}
      <div className={styles["module-login-container"]}>
        {" "}
        {/* Use modular class names */}
        <h2 style={{ height: "20vh", marginTop: 40 }}>Register here</h2>
        <form id="login-form" method="post" onSubmit={handleSubmit}>
          <input type="text" name="name" id="username" placeholder="Username" />
          <input type="email" name="email" id="email" placeholder="Email" />
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Password"
          />
          <button className={styles["register-btn"]}>Register</button>{" "}
          {/* Use modular class name */}
        </form>
        <div id="error-message" style={{ color: "red" }}>
          {error.msg}
        </div>
      </div>
    </div>
  );
}
