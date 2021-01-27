import React, { useContext } from "react";
import { Redirect } from "react-router-dom";
import AuthContext from "../Utils/context/AuthContext";
import AuthRoute from "./AuthRoute";

function AdminRoute({ children, ...rest }) {
  const context = useContext(AuthContext);

  const isAdmin = context.isAdmin;

  let render;

  if (isAdmin === false) {
    render = <Redirect to="/" />;
  } else if (isAdmin) {
    render = children;
  } else {
    render = <div className="flex-1"/>;
  }

  return <AuthRoute {...rest}>{render}</AuthRoute>;
}

export default AdminRoute;
