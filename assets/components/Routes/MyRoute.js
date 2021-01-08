import React from "react";
import { Route } from "react-router-dom";
import NavBar from "../NavBar/NavBar";
import Footer from "../Footer/Footer";

function MyRoute({ children, ...rest }) {
  return (
    <React.Fragment>
      <NavBar />
      <Route {...rest}>{children}</Route>
      <Footer />
    </React.Fragment>
  );
}

export default MyRoute;
