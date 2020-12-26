import React from "react";
import { Redirect, Route } from "react-router-dom";

function AuthRoute({ children, ...rest }) {
  var isAuth = false; //todo

  return <Route {...rest}>{!isAuth ? <Redirect to="/" /> : children}</Route>;
}

export default AuthRoute;
