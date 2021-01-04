import React from "react";
import { Redirect, useLocation } from "react-router-dom";
import MyRoute from "./MyRoute";

function AuthRoute({ children, ...rest }) {
  const location = useLocation();

  const token = localStorage.getItem("remember");
  const isForm =
    location.pathname === "/login" || location.pathname === "/register";

  let render;
  if (!token && !isForm) {
    render = <Redirect to="/login" />;
  } else if (token && isForm) {
    render = <Redirect to="/" />;
  } else {
    render = children;
  }

  return <MyRoute {...rest}>{render}</MyRoute>;
}

export default AuthRoute;
