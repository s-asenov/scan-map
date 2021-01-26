import React, { useContext } from "react";
import { Redirect, useLocation } from "react-router-dom";
import MyRoute from "./MyRoute";
import AuthContext from "../Utils/context/AuthContext";

function AuthRoute({ children, ...rest }) {
  const location = useLocation();
  const context = useContext(AuthContext);

  const isAuth = context.isAuth;

  const isForm =
    location.pathname === "/login" || location.pathname === "/register";

  let render;

  if (isAuth === false && isForm === false) {
    render = <Redirect to="/login" />;
  } else if (isForm === true && isAuth === true) {
    render = <Redirect to="/" />;
  } else {
    render = children;
  }

  return <MyRoute {...rest}>{render}</MyRoute>;
}

export default AuthRoute;
