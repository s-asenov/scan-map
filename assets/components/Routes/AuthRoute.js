import React, { useContext } from "react";
import { Redirect, useLocation } from "react-router-dom";
import MyRoute from "./MyRoute";
import AuthContext from "../Utils/context/AuthContext";

/**
 * The AuthRoute component functions as a react router dom Route components with
 * a authentication implementation using Context API
 */
function AuthRoute({ children, ...rest }) {
  const location = useLocation();
  const context = useContext(AuthContext);

  const isAuth = context.isAuth;

  const isForm =
    (location.pathname === "/login" && !location.unauthorized) ||
    location.pathname === "/register";

  let render;

  if (isAuth === null) {
    render = <div className="flex-1" />;
  } else if (isAuth === false && isForm === false) {
    render = <Redirect to="/login" />;
  } else if (isForm === true && isAuth === true) {
    render = <Redirect to="/terrains" />;
  } else {
    render = children;
  }

  return <MyRoute {...rest}>{render}</MyRoute>;
}

export default AuthRoute;
