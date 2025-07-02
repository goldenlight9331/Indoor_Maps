import { useNavigate } from "react-router-dom";
import { getToken, removeToken } from "../services/LocalStorageService";
import { useLogoutUserMutation } from "../services/userAuthApi";
import { unsetUserToken } from "../features/authSlice";
import { useDispatch } from "react-redux";

export default function Logout() {
  const token = getToken();
  const [logoutUser] = useLogoutUserMutation();
  const navigate = useNavigate();
  const dispatch = useDispatch();

  const handleLogOut = async () => {
    const res = await logoutUser({ token });
    dispatch(unsetUserToken(res));
    removeToken("token");
    navigate("/");
    //  {
    //   console.error("Logout failed:", res.error);
    // }
  };

  return (
    <>
      <button className="btn btn-danger" onClick={handleLogOut}>
        LogOut
      </button>
    </>
  );
}
