import React from "react";
import { Route } from "react-router-dom";
import NavBar from "../NavBar/NavBar";

function MyRoute({ children, ...rest }) {
  return (
    <React.Fragment>
      <NavBar />
      <Route {...rest}>{children}</Route>
    </React.Fragment>
  );
}

export default MyRoute;
