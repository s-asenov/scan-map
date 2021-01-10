import React from "react";
import { Route } from "react-router-dom";
import NavBar from "../NavBar/NavBar";
import Footer from "../Footer/Footer";

function MyRoute({ children, ...rest }) {
  return (
    <React.Fragment>
      {/* <div id="page-content"> */}
      <NavBar />
      <Route {...rest}>{children}</Route>
      {/* </div> */}
      <Footer />
    </React.Fragment>
  );
}

export default MyRoute;
