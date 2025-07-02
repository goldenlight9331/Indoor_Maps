import Logout from "../auth/Logout";
import { Link } from "react-router-dom";

export default function Dashboard() {
  return (
    <>
      <div className="container mt-5">
        <Link className="btn btn-success m-5" to="/">
          Back to Home
        </Link>
        <Logout />
      </div>
    </>
  );
}
