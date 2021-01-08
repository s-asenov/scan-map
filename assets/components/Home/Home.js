import React, { useContext } from "react";
import { Container } from "react-bootstrap";
import { useLocation } from "react-router-dom";
import { isAuth } from "../../helpers/auth";
import AboutUs from "./AboutUs/AboutUs";
import Advantages from "./Advantages/Advantages";
import Contacts from "./Contacts/Contacts";
import Heading from "./Heading/Heading";
import "./Home.css";

function Home() {
  const location = useLocation();

  if (location.redirected) {
    console.log("redirected");
  }

  return (
    <React.Fragment>
      <Heading isAuth={isAuth()} />
      <Advantages />
      <AboutUs />
      <Contacts />
    </React.Fragment>
  );
}

export default Home;
